<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $searchModel common\models\lab\BookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Create Booking';
$this->params['breadcrumbs'][] = ['label' => 'Manage Booking', 'url' => ['/lab/booking/manage']];
$this->params['breadcrumbs'][] = $this->title;

$DragJS = <<<EOF
/* initialize the external events
-----------------------------------------------------------------*/
$('#external-events .fc-event').each(function() {
    // store data so the calendar knows to render an event upon drop
    $(this).data('event', {
        title: $.trim($(this).text()), // use the element's text as the event title
        stick: true // maintain when user navigates (see docs on the renderEvent method)
    });
    // make the event draggable using jQuery UI
    $(this).draggable({
        zIndex: 999,
        revert: true,      // will cause the event to go back to its
        revertDuration: 0  //  original position after the drag
    });
});
EOF;
$this->registerJs($DragJS);

$JSDayClick = <<<EOF
function (date, allDay, jsEvent, view) { 
    alert('haha');
}
EOF;

$JSCode = <<<EOF
function(start, end) {
    var title = prompt('Event Title:');
    var eventData;
    if (title) {
        eventData = {
            title: title,
            start: start,
            end: end
        };
        $('#w0').fullCalendar('renderEvent', eventData, true);
    }
    $('#w0').fullCalendar('unselect');
}
EOF;
$JSDropEvent = <<<EOF
function(date) {
    alert("Dropped on " + date.format());
    if ($('#drop-remove').is(':checked')) {
        // if so, remove the element from the "Draggable Events" list
        $(this).remove();
    }
}
EOF;
$JSEventClick = <<<EOF
function(calEvent, jsEvent, view) {


    window.open( "/lab/booking/view?id=" + calEvent.id, "_blank", "" );
}
EOF;

?>
<div class="booking-index">
    <div>
        <?= Html::button('<span class="glyphicon glyphicon-plus"></span>  Book Now!', ['value'=>'/lab/booking/create', 'class' => 'btn btn-lg btn-success ','title' => Yii::t('app', "Booking"),'id'=>'btnBooking', 'style'=>'style="left: 50%;margin-right: -50%;"','onclick'=>'addBooking(this.value,this.title)'])?>
        - or -
        <?= Html::button('<span class="glyphicon glyphicon-eye-open"></span>  Track your request!', ['value'=>'/lab/booking/viewbyreference', 'class' => 'btn btn-success btn-lg','title' => Yii::t('app', "View Booking"),'id'=>'btnBooking','onclick'=>'viewBooking(this.value,this.title)'])?>
    </div>

    
    <br><br>

    <div>
        <i style="font-size: 30pt"><b style="color:#00a65a">3 steps on how to submit samples online</b></i>
        <br>
        <br>
        <i style="font-size: 20pt"><b style="color:#3c8dbc">Step 1</b> - Book a request</i>
        <br>
        <br>
        <i style="font-size: 20pt"><b style="color:#3c8dbc">Step 2</b> - Fill up the form</i>
        <br>
        <br>
        <i style="font-size: 20pt"><b style="color:#3c8dbc">Step 3</b> - Track your request using the reference number</i>

    </div>
    <br><br>
    <div>
        <i style="font-size: 20pt"><b style="color:#f39c12">Note: </b> - You may receive a text message from the CRO, if your booking is accepted for the schedule requested. </i>

    </div>
</div>
<script type="text/javascript">
    function addBooking(url,title){
        LoadModal(title,url,'true','700px');
    }

    function viewBooking(url,title){
        LoadModal(title,url,'true','700px');
    }
    $('#btnBooking').click(function(){
        $('.modal-title').html($(this).attr('title'));
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
</script>


