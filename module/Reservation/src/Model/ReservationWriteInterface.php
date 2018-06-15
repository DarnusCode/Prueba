<?php
namespace Reservation\Model;

interface ReservationWriteInterface
{
    /**
     * Add Event to database
     * @param Event $event
     * @return Event
     */
    public function insertEvent(Event $event);
    /**
     * Add attendee to event
     * @param Attendee $attendee
     * @return Attendee
     */
    public function insertAttendee(Attendee $attendee);
    /**
     * Update event, this can be done by event owner or an admin
     * @param Event $event
     * @return Event
     */
    public function updateEvent(Event $event);
    /**
     * Remove attendee from event
     * @param Attendee $attendee
     */
    public function deleteAttendee(Attendee $attendee);
}