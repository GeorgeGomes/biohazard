@extends('layout')

@section('content')
<link href='/stylesheet/fullcalendar.css' rel='stylesheet' />
<link href='/stylesheet/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='/javascript/lib/moment.min.js'></script>
<script src='/javascript/lib/jquery.min.js'></script>
<script src='/javascript/fullcalendar.min.js'></script>
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
        var title = 'available';
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
              dd='0'+dd
          } 
          if(mm<10){
              mm='0'+mm
          } 
          var today = yyyy+'-'+mm+'-'+dd;
          var mm2 = mm+2;
          if(mm2>12){
            var yyyy2 = yyyy+1;
            var mm2 = mm2%12;
          }else{
            var yyyy2 = yyyy;
          }
          if(mm2<10){
              mm2='0'+mm2
          } 
          var limitdate = yyyy2+'-'+mm2+'-'+dd;
          if(check < today)
          {
            alert('Você não está permitido escolher datas anteriores.');
          }
          else if(check > limitdate)
          {
            alert('Você só pode escolher datas de um período de 2 meses.');
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
      var r = confirm("Você tem certeza que deseja deletar?");
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
</style>

<div class="box box-success">

<br><br><br>
<center>
                  <!-- Button trigger modal -->
                  <button type="button" style="float:right" class="btn btn-green" data-toggle="modal" data-target="#myModal">
                    Ajuda?
                  </button>
                  <input type="hidden" name="id" value="{{$provider->id}}">
                  <!-- Modal -->
                  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                          <h4 class="modal-title" id="myModalLabel">Usando o Calendário de Disponibilidade</h4>
                        </div>
                        <div class="modal-body" style="align:left">
                          <p>Você pode colocar a disponibilidade por:
                            <ol>
                              <li>Selecionando a data no calendário.</li>
                              <li>Carregando uma data já selecionada.</li>
                              <li>Deslocando a data de um local para outro.</li>
                              <li>Clicando na opção de Todo o Dia para ficar disponível o dia inteiro.</li>
                            </ol>
                          </p>
                          <p>Cliquue em Salvar Disponibilidade para guardar os dados no Banco de Dados. Após os dados serem salvos, um Pop-up de confirmação vai aparecer.</p><br>
                          <p>Você pode remover um evento salvo clicando nele. Um Pop-up vai aparecer perguntando para confirmar. Você pode escolher Sim para deletar ou Cancelar para retornar a Visão do Calendário.</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Entendi</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id='calendar'></div><br>
                  <span class="btn btn-info" id="storeav">Salvar Disponibilidade</span>
                  <br /><br />
              </center>


</div>







<div class="row third-portion">
    <div class="tab-content">
        <div class="tab-pane fade" id="option3">
            <div class="row tab-content-caption">
                <div class="container">
                    <div class="col-md-10 big-text">
                        <p><?= $title ?></p>
                    </div>
                </div>
            </div>
            <div class="row editable-content-div">
             <div class="container">

              <center>
                  <!-- Button trigger modal -->
                  <button type="button" style="float:right" class="btn btn-green" data-toggle="modal" data-target="#myModal">
                    Ajuda?
                  </button>
                  <input type="hidden" name="id" value="{{$provider->id}}">
                  <!-- Modal -->
                  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                          <h4 class="modal-title" id="myModalLabel">Usando o Calendário de Disponibilidade</h4>
                        </div>
                        <div class="modal-body" style="align:left">
                          <p>Você pode colocar a disponibilidade por:
                            <ol>
                              <li>Selecionando a data no calendário.</li>
                              <li>Carregando uma data já selecionada.</li>
                              <li>Deslocando a data de um local para outro.</li>
                              <li>Clicando na opção de Todo o Dia para ficar disponível o dia inteiro.</li>
                            </ol>
                          </p>
                          <p>Cliquue em Salvar Disponibilidade para guardar os dados no Banco de Dados. Após os dados serem salvos, um Pop-up de confirmação vai aparecer.</p><br>
                          <p>Você pode remover um evento salvo clicando nele. Um Pop-up vai aparecer perguntando para confirmar. Você pode escolher Sim para deletar ou Cancelar para retornar a Visão do Calendário.</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Entendi</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id='calendar'></div><br>
                  <span class="btn btn-info" id="storeav">Salvar Disponibilidade</span>
                  <br /><br />
              </center>
            </div>
          </div>
  </div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
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
      url: "{{ URL::Route('AdminProviderAvailabilitySubmit',$provider->id) }}",
      data: {'proavis': proavis,'proavie': proavie,'length': pl},
      success:function(data){
        if(data.success==true){
            // handle data array
            alert('Sua Disponibilidade dessa semana foi salva. Por Favor proceda para a próxima semana.')
        }
        else {
            // nothing returned - error
        }
      }
    
    
    });
  });
});
</script>
@stop
