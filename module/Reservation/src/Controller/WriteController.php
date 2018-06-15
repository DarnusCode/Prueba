<?php
namespace Reservation\Controller;

use InvalidArgumentException;

use Zend\Mvc\Controller\AbstractActionController;
use Reservation\Model\ReservationListInterface;
use Reservation\Model\ReservationWriteInterface;
use Zend\Session\Container;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Reservation\Model\Event;
use Reservation\Model\Attendee;

class WriteController extends AbstractActionController
{
    // Properties
    /**
     * So we can perform READS
     *
     * @var ReservationListInterface
     */
    private $reservationList;
    /**
     * So we can perform UPDATES, INSERTS AND DELETES
     *
     * @var ReservationWriteInterface
     */
    private $reservationWrite;
    /**
     *
     * @var Container $_session
     */
    private $_session;
    // Constructor
    /**
     * Initialize interfaces
     *
     * @param UserInterface $userList
     * @param UserCommandInterface $userCommand
     * @param UserLogInterface $userLog
     */
    public function __construct(ReservationListInterface $reservationList, ReservationWriteInterface $reservationWrite){
        $this->reservationList = $reservationList;
        $this->reservationWrite = $reservationWrite;
        // Grab user's information in session
        $this->_session = new Container(SESSION_NAME);
        if( empty($_SESSION['csrf-token']) ){
            $_SESSION['csrf-token'] = base64_encode(openssl_random_pseudo_bytes(20));
        }
    }
    /**
     * Display form to create an event
     */
    public function eventCreateAction(){
        // If user has already signed in, redirect to home page
        if( $this->_session->offsetGet('userId')===null ){
            return $this->redirect()->toRoute('login');
        }        
        $room = null;
        $dateSelected = null;
        $hourSelected = null;
        // The form sould be called from an AJAX request
        if ( $this->getRequest()->isXmlHttpRequest() ) {            
            $roomId = $this->params()->fromRoute("roomId", 0);
            $dateSelected = $this->params()->fromRoute("date", null);
            $hourSelected = $this->params()->fromRoute("hour", null);
            $room = $this->reservationList->findRoom($roomId);
        }
        // Declare a form view
        $view = new ViewModel([            
            'room' => $room,
            'dateSelected' => $dateSelected,
            'hourSelected' => $hourSelected,
            'organizer' => [ 
                'name' => $this->_session->offsetGet('userName'), 
                'email' => $this->_session->offsetGet('userEmail'),
            ],
            'token' => $_SESSION['csrf-token'],
        ]);
        // No need to use layout
        $view->setTerminal( true );
        return $view;
    }
    /**
     * Get data from post, validate and process it
     */
    public function eventSaveAction(){
        $status = "success";
        $message = "";
        // The form sould be called from an AJAX request        
        if( $this->getRequest()->isXmlHttpRequest() ){
            try {
                // read data from POST
                $data = $this->params()->fromPost();
                //var_dump($data); die();
                $token = $data['inputCSRFToken'];
                // compare tokens to avoid CSRF
                if( $_SESSION['csrf-token'] != $token ){
                    throw new \InvalidArgumentException("Token inv&aacute;lido");
                }                
                // All good ?
                $this->validateData($data);
                $dateSelected = $data['inputDateSelected'];
                $startAt = $data['inputStartAt'];
                $finishAt = $data['inputFinishAt'];
                $title = $data['inputTitle'];
                $description = $data['inputDescription'];
                // parse to UNIXTIME
                $startAtUnixFormat = $this->getUnixDate($dateSelected, $startAt);
                $finishAtUnixFormat = $this->getUnixDate($dateSelected, $finishAt);
                // who created the event ?
                $organizerId = $this->_session->offsetGet('userId');
                $organizerName = $this->_session->offsetGet('userName');
                $organizerEmail = $this->_session->offsetGet('userEmail');
                // Create event
                $newEvent = new Event(0, $data['inputRoomId'], $title, $description, $startAtUnixFormat, $finishAtUnixFormat, $organizerId);
                $event = $this->reservationWrite->insertEvent($newEvent);
                if( $event->getId() > 0 ){
                    $room = $this->reservationList->findRoom( (integer)$data['inputRoomId'] );
                    // Add organizer first
                    $attendee = new Attendee(0, $event->getId(), $organizerName, $organizerEmail);
                    $this->reservationWrite->insertAttendee($attendee);
                    // Add attendees
                    for( $i = 1; $i <= $room->getCapacity(); $i++ ){
                        $attendeeName = isset( $data['inputAttendeeName'.$i] ) && !empty( $data['inputAttendeeName'.$i] ) ? trim( $data['inputAttendeeName'.$i] ) : "";
                        $attendeeEmail = isset( $data['inputAttendeeEmail'.$i] ) && !empty( $data['inputAttendeeEmail'.$i] ) ? trim( $data['inputAttendeeEmail'.$i] ) : "";
                        // All those which actually have data
                        if( !empty($attendeeName) && !empty($attendeeEmail) ){
                            $attendee = new Attendee(0, $event->getId(), $attendeeName, $attendeeEmail);
                            $this->reservationWrite->insertAttendee($attendee);
                        }
                    }
                }
            } catch ( \InvalidArgumentException $e){
                $status = "error";
                $message = $e->getMessage();
            } catch ( \Exception $e) {
                $status = "error";                
                $message = $e->getMessage()." Trace ";
                $trace = explode( "#", $e->getTraceAsString() );
                foreach( $trace as $item  ){
                    $message .= $item."\r\n";                    
                }
                $logId = time();                
                error_log("|ErrorID:{$logId}|=== {$message} "."\r\n");
                $message = "[{$logId}]Error guardando datos, consulte a su administrador del sistema";                
            } 
        }        
        return new JsonModel([
            'status' => $status,
            'message' => $message,
        ]);        
    }
    /**
     * 
     * @param array $data
     * @throws InvalidArgumentException
     */
    private function validateData( array $data ){
        $dateSelected = $data['inputDateSelected'];
        $startAt = $data['inputStartAt'];
        $finishAt = $data['inputFinishAt'];
        $title = $data['inputTitle'];
        $description = $data['inputDescription'];
        
        if( !isset($data['inputRoomId']) || (integer)$data['inputRoomId']==0 ){
            throw new \InvalidArgumentException("Sala inv&aacute;lida");
        }        
        if( !isset($data['inputTitle']) || trim(empty($data['inputTitle'])) ){
            throw new \InvalidArgumentException("T&iacute;tulo inv&aacute;lido");
        }
        if( !isset($data['inputDescription']) || trim(empty($data['inputDescription'])) ){
            throw new \InvalidArgumentException("Descripci&oacute;on inv&aacute;lida");
        }
        $startAtUnixFormat = $this->getUnixDate($dateSelected, $startAt);
        $finishAtUnixFormat = $this->getUnixDate($dateSelected, $finishAt);
        // start MUST be less than finish
        if( $startAtUnixFormat >= $finishAtUnixFormat  ){
            $logId = time();
            error_log("|ErrorID:{$logId}|===FechaInicio {$startAtUnixFormat}, FechaFin {$finishAtUnixFormat}"."\r\n");
            throw new \InvalidArgumentException("[{$logId}]Error en las fechas, inicio: ".$dateSelected." ".$startAt.":00:00"." , fin: ".$dateSelected." ".$finishAt.":00:00" );            
        }
    }
    /**
     * 
     * @param string $date E.g. 12-06-2018
     * @param integer $hour E.g. 8, 9, 10
     */
    private function getUnixDate( $date, $hour ){
        $result = strtotime( $date." ".$hour.":00:00" );
        return $result;
    }
}