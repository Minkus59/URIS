<aside class="Calendrier">
<div id="eventCalendarShowDescription"></div>
<script>
    $(document).ready(function() {
        $("#eventCalendarShowDescription").eventCalendar({
            eventsjson: '/lib/calendar/json/events.json.php',
            showDescription: true,
            locales: '/lib/calendar/json/locale.fr.json'
        });
    });
</script>
</aside>