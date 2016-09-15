@extends('web.providerLayout')

@section('content')
<link href="{{asset('/stylesheet/fullcalendar.css')}}" rel='stylesheet' />
<link href="{{asset('/stylesheet/fullcalendar.print.css')}}" rel='stylesheet' media='print' />
<script src="{{asset('/javascript/lib/moment.min.js')}}"></script>
<script src="{{asset('/javascript/lib/jquery.min.js')}}"></script>
<script src="{{asset('/javascript/fullcalendar.min.js')}}"></script>
<script>
  $(document).ready(function() {
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: ''
      },
      defaultDate: new Date(),
      selectable: true,
      selectHelper: true,
      defaultView: 'agendaWeek',
      minTime: "05:00:00",
      maxTime: "21:00:00",
      scrollTime: "05:00:00",
      allDaySlot: false,
      slotEventOverlap : false,
      selectOverlap: false,
      dragOpacity: .40,
      slotDuration: "01:00:00",
      timeFormat: "h a",
      select: function(start, end, allday) {
        var title = '{{trans("user_provider_web.trip_requested")}}';
        var eventData;
        if (title) {
          eventData = {
            title: title,
            start: start,
            end: end
          };
          var check = start.format('YYYY-MM-DD');
          var today = new Date();
          var dd = today.getDate();
          var mm = today.getMonth()+1; //January is 0!
          var yyyy = today.getFullYear();
          if(dd<10){
              dd='0'+dd;
          } 
          if(mm<10){
              mm1='0'+mm;
          }
          var today = yyyy+'-'+mm1+'-'+dd;
          var mm2 = mm+2;
          if(mm2>12){
            var yyyy2 = yyyy+1;
            var mm2 = mm2%12;
          }else{
            var yyyy2 = yyyy;
          }
          if(mm2<10){
              mm2='0'+mm2;
          }
          var limitdate = yyyy2+'-'+mm2+'-'+dd;
          if(check < today)
          {
            alert('{{trans("user_provider_web.before_date")}}');
          }
          else if(check > limitdate)
          {
            alert('{{trans("user_provider_web.two_weeks")}}.');
          }
          else
          {
              $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
              callback(start,end);
          }
        }
        $('#calendar').fullCalendar('unselect');
      },
      eventDrop: function(event,dayDelta,minuteDelta,revertFunc) {
        callback(event.start,event.end);
      },
      eventResize: function(event, delta, revertFunc) {
          callback(event.start,event.end);
      },
      editable: true,
      eventLimit: true, // allow "more" link when too many events
      events: {
        url: '/php/get-events.php'
      },
      loading: function(bool) {
        $('#loading').toggle(bool);
      },
      eventClick: function(event, jsEvent, view) {
        confirmdelete(event);
      },
      editable: true,
      eventLimit: true, // allow "more" link when too many events
      events: {{$pvjson}} 

    });
  });

  function confirmdelete(event) {
      var txt;
      var r = confirm("{{trans('user_provider_web.sure_delete')}}?");
      if (r == true) {
          $('#calendar').fullCalendar('removeEvents',event._id);
      } else {
          // Do nothing
      }
  }

  function callback(start, end){
    console.log(start.format('YYYY-MM-DD HH:mm:ss')+" "+end.format('YYYY-MM-DD HH:mm:ss'));
    // Ajax send data to php
  }

</script>
<style>
  #calendar {
    max-width: 44%;
    margin: 0 auto;
  }

  h2{
    color:#000; 
  }
  th{
    color:#000;
  }
  span{
    color:#1c1c1c;
  }

</style>
<div class="col-md-12 beige-wrapper-white">
  <center>
      <!-- Button trigger modal -->
      <button type="button" class="btn btn-green" data-toggle="modal" data-target="#myModal">
        {{trans('user_provider_web.help')}}?
      </button>



      <!-- Modal -->
      <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{trans('user_provider_web.close')}}</span></button>
              <h4 class="modal-title" id="myModalLabel">{{trans('user_provider_web.calendar_use')}}</h4>
            </div>
            <div class="modal-body" style="align:left">
              <p>{{trans('user_provider_web.choose_availability_message')}}:
                <ol>
                  <li>{{trans('user_provider_web.choose_date_message')}}</li>
                  <li>{{trans('user_provider_web.upload_date_message')}}</li>
                  <li>{{trans('user_provider_web.drag_date_message')}}</li>
                  <li>{{trans('user_provider_web.all_day_date_message')}}</li>
                </ol>
              </p>
              <p>{{trans('user_provider_web.click_save_message')}}</p><br>
              <p>{{trans('user_provider_web.remove_event_message')}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('user_provider_web.understood')}}</button>
            </div>
          </div>
        </div>
      </div>

      <div id='calendar'></div><br>
      <span class="btn btn-info" id="storeav">{{trans('user_provider_web.save_availability')}}</span>
      <br /><br />
  </center>
</div>

<script type="text/javascript">

  $("#storeav").click(function(){
    provav = $('#calendar').fullCalendar('clientEvents');
    console.log(provav);
    var pl = provav.length;
    proavis = new Array(pl);
    proavie = new Array(pl);
    for(var i=0;i<pl;i++){
      proavis[i] = provav[i].start.format('YYYY-MM-DD HH:mm::ss');
      proavie[i] = provav[i].end.format('YYYY-MM-DD HH:mm::ss');
    }
    console.log(proavis+" "+proavie);
    $.ajax({
      type: "POST",
      url: "{{route('provideravailabilitySubmit')}}",
      data: {'proavis': proavis,'proavie': proavie,'length': pl},
      success:function(data){
        if(data.success==true){
            // handle data array
            alert('{{trans("user_provider_web.save_availability_message")}}.')
        }
        else {
            // nothing returned - error
        }
      }
    });
  });
</script>
@stop
