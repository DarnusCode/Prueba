<?php
namespace Reservation\Model;

interface ReservationListInterface
{
    /**
     * Find Room by id
     * @param integer $id
     * @return Room
     */
    public function findRoom($id);
    /**
     * List all rooms available
     * @param string $paginated
     * @return Room[]
     */
    public function findAllRooms($paginated = false);
    /**
     * List all events
     * @param string $paginated
     * @return Event[]
     */
    public function findAllEvents($paginated = false);
    /**
     * List events per date
     * @param integer $date as a Unixtimestamp e.g.time()
     */
    public function findEventsByDate($date);
    /**
     * Find event by id
     * @param integer $id Event id
     * @return Event
     */
    public function findEvent($id);
    /**
     * List all the attendees by event
     * @param Event $event
     * @return Attendee[]
     */
    public function findAttendees(Event $event);    
}