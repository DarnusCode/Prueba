<?php
namespace Reservation\Model;

use RuntimeException;
use InvalidArgumentException;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Sql;
use Zend\Validator\Db\RecordExists;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Expression;

class ReservationDbWrite implements ReservationWriteInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;
    // Set table names
    protected $_rooms = 'rooms';
    protected $_events = 'events';
    protected $_attendees = 'attendees';
    
    /**
     * 
     * @param AdapterInterface $db
     */
    public function __construct(AdapterInterface $db){
        // Set connection
        $this->db = $db;        
    }
    /**
     * {@inheritDoc}
     * @see \Model\ReservationWriteInterface::deleteAttendee()
     */
    public function deleteAttendee(Attendee $attendee) {
        if( !$attendee->getId() ){
            throw new RuntimeException("Attendee does not exist, please verify");
        }
        // Prepare what it needs to be removed
        $delete = new Delete( $this->_attendees );
        $delete->where( [ 'id = ?'=> $attendee->getId() ] );
        // Set sql
        $sql = new Sql( $this->db );
        $statement = $sql->prepareStatementForSqlObject($delete);
        $statement->execute();        
    }
    /**
     * {@inheritDoc}
     * @see \Model\ReservationWriteInterface::insertAttendee()
     */
    public function insertAttendee(Attendee $attendee) {
        // Perform some validations like:
        // 1.- Event ID must exist
        if( !$this->eventExists($attendee->getEventId()) ){
            throw new RuntimeException( 'Database error occurred during attendee insert operation, event does not exist' );
        }        
        // 2.- Email is a proper email address
        $validateEmail = new \Zend\Validator\EmailAddress();
        if( !$validateEmail->isValid( $attendee->getEmail() ) ){
            throw  new \InvalidArgumentException( 'Correo inv&aacute;lido '.$attendee->getEmail() );
        }
        // 3.- Email hasn't been set already
        if( $this->eventAttendeeExists($attendee) ){
            throw  new \InvalidArgumentException( 'Correo ya existe para &eacute;ste evento '.$attendee->getEmail() );
        }
        // Seems everything went well, set data to be inserted
        $data = [
            'event_id' => $attendee->getEventId(),
            'name' => $attendee->getName(),
            'email' => $attendee->getEmail(),
        ];
        $insert = new Insert( $this->_attendees );
        $insert->values($data);
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        // DEBUG ONLY
        //        echo $sql->buildSqlString( $insert ); die();
        $result = $statement->execute();
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException( 'Database error occurred during attendee insert operation' );
        }
        // get new attendee id and return as object
        $attendeeId = $sql->getAdapter()->getDriver()->getLastGeneratedValue();
        return new Attendee( $attendeeId, $attendee->getEventId(), $attendee->getName(), $attendee->getEmail() );        
    }

    /**
     * {@inheritDoc}
     * @see \Model\ReservationWriteInterface::insertEvent()
     */
    public function insertEvent(Event $event)
    {
        // TODO ! validate current event does not overlap another one
        if( $this->eventDateOcuppied($event) ){
            throw  new \InvalidArgumentException( 'La sala ya est&aacute; ocupada en &eacute;ste horario '.$event->getTitle() );
        }        
        $insert = new Insert( $this->_events );
        $insert->values([
            'room_id' => $event->getRoomId(),
            'title' => strip_tags( $event->getTitle() ),
            'description' => strip_tags( $event->getDescription() ),
            'start_at' => $event->getStartAt(),
            'finish_at' => $event->getFinishAt(),
            'user_id' => $event->getUserId(),
        ]);
        $sql = new Sql( $this->db );
        $statement = $sql->prepareStatementForSqlObject($insert);
        // DEBUG ONLY
        //        echo $sql->buildSqlString( $insert ); die();
        $result = $statement->execute();
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException( 'Database error occurred during event insert operation' );
        }
        $eventId = $sql->getAdapter()->getDriver()->getLastGeneratedValue();
        return new Event($eventId, $event->getRoomId(), $event->getTitle(), $event->getDescription(), $event->getStartAt(), $event->getFinishAt(), $event->getUserId() );        
    }

    /**
     * {@inheritDoc}
     * @see \Model\ReservationWriteInterface::updateEvent()
     */
    public function updateEvent(Event $event) {
        // TODO ! validate current event does not overlap another one
        if( $this->eventDateOcuppied($event) ){
            throw  new \InvalidArgumentException( 'La sala ya est&aacute; ocupada en &eacute;ste horario '.$event->getTitle() );
        }
        $update = new Update( $this->_events );
        $update->set([
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'start_at' => $event->getStartAt(),
            'finish_at' => $event->getFinishAt(),
        ]);
        $update->where([ 'id = ?' => $event->getId() ]);
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        // DEBUG ONLY
        //        echo $sql->buildSqlString( $update ); die();
        $result = $statement->execute();        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException( 'Database error occurred during event update operation' );
        }
        return new Event($event->getId(), $event->getRoomId(), $event->getTitle(), $event->getDescription(), $event->getStartAt(), $event->getFinishAt(), $event->getUserId() );
    }
    /**
     * Verify an event exists before adding attendees
     * @param integer $id
     * @return boolean
     */
    private function eventExists($id) {
        $exists = new RecordExists( [ 'table' => $this->_events, 'field' => 'id', 'adapter' => $this->db, ]);
        return $exists->isValid( $id );        
    }
    /**
     * Verify for duplicated attendees
     * 
     * Validates the record exists for multiple columns
     * 
     * See for details:
     * @link https://framework.zend.com/manual/2.3/en/modules/zend.validator.db.html
     * 
     * @param Attendee $attendee
     * @return boolean
     */
    private function eventAttendeeExists(Attendee $attendee) {
        $select = new Select();
        $select->from( $this->_attendees );
        $select->where( [
            'event_id' => $attendee->getEventId(),
            'email' => $attendee->getEmail(),            
        ]);
        $exists = new RecordExists( $select );
        // We still need to set our database adapter
        $exists->setAdapter( $this->db );        
        return $exists->isValid( $attendee->getId() );        
    }
    /**
     * Verify event does not overlap for insertions or updates
     * @param Event $event
     * @return boolean
     */
    private function eventDateOcuppied(Event $event) {
        $select = new Select();
        $select->from( $this->_events );
        // Create betweens
        $betweenStartAt = new Expression("BETWEEN ({$event->getStartAt()}+1) AND ({$event->getFinishAt()}-1) ");        
        $betweenNewStartAt = new Expression("BETWEEN start_at AND finish_at ");
        $select->where( [
            'id != ?' => (integer)$event->getId(),
            'room_id = ?' => (integer)$event->getRoomId(),
            '(start_at ? ' => $betweenStartAt, // Notice! open parenthesis 
        ]);
        // Build date range        
        $select->where( [ "finish_at ? " => $betweenStartAt, ], Predicate::OP_OR );
        $select->where( [ "(".$event->getStartAt()."+1) ? )" => $betweenNewStartAt, ], Predicate::OP_OR );// Notice! closing parenthesis
        
        $exists = new RecordExists( $select );
// DEBUG ONLY
        //$sql = new Sql( $this->db );
        //$statement = $sql->prepareStatementForSqlObject($select);        
        //echo $sql->buildSqlString( $select ); die();
// DEBUG ONLY
        
        // We still need to set our database adapter
        $exists->setAdapter( $this->db );
        return $exists->isValid( $event->getId() );        
    }    
}

