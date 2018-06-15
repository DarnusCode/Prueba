<?php
namespace Reservation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Reservation\Model\ReservationListInterface;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use User\Model\UserListInterface;
use User\Model\User;
use Reservation\Model\Event;

class ListController extends AbstractActionController
{
    /**
     *
     * @var $reservationList
     */
    private $reservationList;
    /**
     * 
     * @var UserListInterface
     */
    private $userList;
    private $_session;
    /**
     * 
     * @param ReservationListInterface $reservationList
     */
    public function __construct(ReservationListInterface $reservationList, UserListInterface $userList){
        $this->reservationList = $reservationList;
        $this->userList = $userList;
        // Store user's information in session
        $this->_session = new Container(SESSION_NAME);
    }
    /**
     * Defualt action, display current date and events
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction(){
        // get current date in format day-month-year
        $today = date( 'd-m-Y', time() );
        // parse to unix time so we can have a number
        $todayUnixTime = strtotime($today);
        // get param from route        
        $currentDate = $this->params()->fromRoute('date', null);
        // Need to make sure date is valid
        if( strtotime($currentDate)==false || $currentDate==null ){
            $currentDate = date( 'd-m-Y', time() );            
        }
        $currentUnixDate = strtotime($currentDate);
        $currentHour = (integer)$this->getCurrentHour( (integer)$todayUnixTime, (integer)$currentUnixDate );
        // Get dates for pagination
        $prevDate = $this->getFormattedDate('d-m-Y', '-1 day', $currentDate);        
        $nextDate = $this->getFormattedDate('d-m-Y', '+1 day', $currentDate);
        $prevWeek = $this->getFormattedDate('d-m-Y', '-7 day', $currentDate);
        $nextWeek = $this->getFormattedDate('d-m-Y', '+7 day', $currentDate);
        // Get current reservations                
        $rooms = $this->reservationList->findAllRooms();
        $events = $this->reservationList->findEventsByDate($currentUnixDate);
        // Have an array to store events                        
        $jsonEvent = $this->getEventsInArray($events);
        // Send results to view
        return new ViewModel([
            'rooms' => $rooms,
            'jsonEvents' => $jsonEvent,
            'currentHour' => $currentHour,
            'currentDate' => $currentDate,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'prevWeek' => $prevWeek,
            'nextWeek' => $nextWeek,                        
        ]);        
    }
    /**
     * List details for a particular event
     */
    public function eventDetailsAction(){
        $organizer = null;
        $event = null;
        $attendes = null;
        $room = null;
        // In order to see event details an AJAX call must be placed
        if( $this->getRequest()->isXmlHttpRequest() ) {
            // get param from route
            $eventId = $this->params()->fromRoute('id', 0);
            $event = $this->reservationList->findEvent($eventId);
            $room = $this->reservationList->findRoom( $event->getRoomId() );
            $organizer = $this->userList->findUser( $event->getUserId() );
            $attendes = $this->reservationList->findAttendees($event);            
        }        
        // Declare a form view
        $view = new ViewModel([
            'organizer' => $organizer,
            'event' => $event,
            'attendees' => $attendes,
            'room' => $room,
        ]);
        // No need to use layout
        $view->setTerminal( true );
        return $view;
    }
    /**
     * Calculate current hour based on day selected from route params and current hour in UNIX time
     * @param integer $today
     * @param integer $selectedDay
     * @return integer
     */
    private function getCurrentHour( int $today, int $selectedDay ) : int{
        $currentHour = date('H', time());
        if( $selectedDay < $today ) { // yesterday, can't schedule events
            $currentHour = 24;            
        }
        if( $selectedDay > $today ) { // tomorrow, then should be able to create events
            $currentHour = 0;
        }
        return $currentHour;
    }
    /**
     * Parse a date to format and also adds or subtract days
     * @param string $format
     * @param string $toAdd
     * @param string $date
     * @return string
     */
    private function getFormattedDate( string $format, string $toAdd, string $date){
        return date( $format, strtotime( $toAdd, strtotime($date) ) );
    }
    /**
     * 
     * @param array $events
     * @return array
     */
    private function getEventsInArray($events) : array{
        $jsonEvent = array();
        foreach ( $events as $event ){
            $jsonEvent[$event->getRoomId()][ (integer)date( 'H', $event->getStartAt() ) ] = [
                'EventId' => $event->getId(),
                'Title' => $event->getTitle(),
                'Description' => $event->getDescription(),
                'StartAt' => (integer)date( 'H', $event->getStartAt() ),
                'FinishAt' => (integer)date( 'H', $event->getFinishAt() ),
            ];
        };
        return $jsonEvent;        
    }
    
}