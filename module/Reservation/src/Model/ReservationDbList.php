<?php
namespace Reservation\Model;

use InvalidArgumentException;
use RuntimeException;

use Zend\Hydrator\Reflection as ReflectionHydrator;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

use Reservation\Model\Event;

class ReservationDbList implements ReservationListInterface
{
    // Properties, table names
    protected $_rooms = 'rooms';
    protected $_events = 'events';
    protected $_attendees = 'attendees';
    
    /**
     * @var AdapterInterface
     */
    private $db;
    /**
     * @var HydratorInterface
     */
    private $hydrator;
    
    /**
     * @var Room
     */
    private $roomPrototype;
    /**
     * 
     * @var Event
     */
    private $eventPrototype;
    /**
     * 
     * @var Attendee
     */
    private $attendeePrototype;
    
    public function __construct(AdapterInterface $db){
        // Set connection
        $this->db = $db;
        $this->hydrator = new ReflectionHydrator();
        // Set object prototypes
        $this->roomPrototype = new Room(0, '', '', 0, 0, 0);
        $this->eventPrototype = new Event(0, 0, '', '', 0, 0, 0);
        $this->attendeePrototype = new Attendee(0, 0, '', '');
    }
    /**
     * 
     * {@inheritDoc}
     * @see \Reservation\Model\ReservationListInterface::findRoom()
     */
    public function findRoom($id){
        $sql = new Sql($this->db);
        $select    = $sql->select( $this->_rooms );
        $select->where(['id = ?' => $id]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException( sprintf('Failed retrieving room with identifier "%s"; unknown database error.', $id ) );
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->roomPrototype);
        $resultSet->initialize($result);
        $room = $resultSet->current();
        if (!$room) {
            throw new InvalidArgumentException( sprintf('Room with identifier "%s" not found.',$id) );
        }
        return $room;        
    }
    /**
     * 
     * {@inheritDoc}
     * @see \Model\ReservationListInterface::findAllRooms()
     */
    public function findAllRooms($paginated = false){
        $sql = new Sql($this->db);
        $select = $sql->select($this->_rooms);
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        // Are we getting what is expected?
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        $resultSet = new HydratingResultSet( $this->hydrator, $this->roomPrototype );
        // Are we using pagination ?
        if( $paginated ){
            $resultSet->setObjectPrototype( $this->roomPrototype );
            // Create a new pagination adapter object:
            $paginatorAdapter = new DbSelect($select, $this->db, $resultSet);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $resultSet->initialize($result);
            return $resultSet;
        }        
    }    
    /**
     * {@inheritDoc}
     * @see \Model\ReservationListInterface::findAllEvents()
     */
    public function findAllEvents($paginated = false){
        $sql = new Sql($this->db);
        $select = $sql->select($this->_events);
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        // Are we getting what is expected?
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        $resultSet = new HydratingResultSet( $this->hydrator, $this->eventPrototype );
        // Are we using pagination ?
        if( $paginated ){
            $resultSet->setObjectPrototype( $this->eventPrototype );
            // Create a new pagination adapter object:
            $paginatorAdapter = new DbSelect($select, $this->db, $resultSet);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $resultSet->initialize($result);
            return $resultSet;
        }        
    }
    /**
     *
     * {@inheritDoc}
     * @see \Reservation\Model\ReservationListInterface::findEventsByDate()
     */
    public function findEventsByDate($date){
        $initDate = $endDate = 0;
        // try to parse $date
        $initDate = $date;
        $endDate = $date + (24*60*60); // plus one day
        
        $sql = new Sql($this->db);
        $select = $sql->select($this->_events);
        $select->where([
            ' start_at >= ?' => $initDate,
            ' start_at < ?' => $endDate,
        ]);
        $select->order('room_id');
        $stmt   = $sql->prepareStatementForSqlObject($select);        
        // DEBUG ONLY
//        echo $sql->buildSqlString( $select ); die();
        $result = $stmt->execute();
        // Are we getting what is expected?
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        $resultSet = new HydratingResultSet( $this->hydrator, $this->eventPrototype );
        $resultSet->initialize($result);
        return $resultSet;
    }
    /**
     * {@inheritDoc}
     * @see \Model\ReservationListInterface::findAttendees()
     */
    public function findAttendees(Event $event) {
        $sql = new Sql($this->db);
        $select = $sql->select($this->_attendees);
        $select->where([ 'event_id = ?' => $event->getId() ]);
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        // Are we getting what is expected?
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        $resultSet = new HydratingResultSet( $this->hydrator, $this->attendeePrototype );
        $resultSet->initialize($result);
        return $resultSet;
    }
    /**
     * {@inheritDoc}
     * @see \Model\ReservationListInterface::findEvent()
     */
    public function findEvent($id) {
        $sql = new Sql($this->db);
        $select    = $sql->select( $this->_events );
        $select->where(['id = ?' => $id]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException( sprintf('Failed retrieving event with identifier "%s"; unknown database error.', $id ) );
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->eventPrototype);
        $resultSet->initialize($result);
        $event = $resultSet->current();
        if (!$event) {
            throw new InvalidArgumentException( sprintf('Event with identifier "%s" not found.',$id) );
        }
        return $event;
    }
    
}