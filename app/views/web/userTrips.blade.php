@extends('web.layout')

@section('content')

<div class="col-md-12 mt">
    @if(Session::has('message'))
            <div class="alert alert-{{ Session::get('type') }}">
                <b>{{ Session::get('message') }}</b> 
            </div>
    @endif
    
  <div class="content-panel">
      <table class="table table-hover" id="trip-table">
          <thead>
          <tr>
              <th>{{trans('user_provider_web.date');}}</th>
              <th>{{trans('customize.Provider')}}</th>
              <th>{{trans('user_provider_web.fare');}}</th>
              <th>{{trans('provider.service_type');}}</th>
          </tr>
          </thead>
          <tbody>
          @foreach($requests as $request)
          <tr class="trip-basic" data-id="{{ route('/user/trip',$request->id)}}">
                <?php 
                    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
                    date_default_timezone_set('America/Sao_Paulo');
                ?>
              <td>{{ strftime('%A, %d de %B de %Y',strtotime($request->request_start_time)) }}</td>
              <td>{{ $request->first_name }} {{ $request->last_name }}</td>
              <td>{{ sprintf2($request->total, 2) }}</td>
              <td>{{ $request->type }}</td>
          </tr>
          <tr class="trip-detail" style="display:none;">
            <td colspan="4"><center>{{trans('user_provider_web.Loading')}}...</center></td>
          </tr>
          @endforeach
          
          </tbody>
      </table>
    </div>
</div>

<script type="text/javascript">
  
  $(function() {
    $( ".trip-basic" ).click(function() {
      var $this = $(this);
      var id = $(this).data('id');
      $this.next().toggle();
      $.ajax({ url: id,
           type: 'get',
           success:
           function(msg) {
             if(msg === 'false'){
                alert('{{trans('user_provider_web.no_result')}}');
             }
             else{
                $this.next().html(msg);
             } 
           }
          });
           
    });


  });

</script>

    <!--script for this page-->
    <script type="text/javascript">
      var tour = new Tour(
        {
          name: "userappHome",
        });

        // Add your steps. Not too many, you don't really want to get your users sleepy
        tour.addSteps([
          {
            element: "#flow1", 
            title: "{{trans('user_provider_web.request_trip')}}", 
            content: "{{trans('user_provider_web.request_trip_message')}}",  
          }
       ]);

     // Initialize the tour
     tour.init();

     // Start the tour
     tour.start();
</script>

@stop 