@extends('website.layout')

@section('content')

<h2>{{ trans('website.terms_and_conditions');}}</h2>
<hr>
<p>{{ trans('website.app_terms_and_conditions');}}</p>
<p>{{ trans('website.by_select_terms_and_conditions');}}
  <ol type="a">
    <li>{{ trans('website.acknowledge_terms_and_conditions');}}</li>
    <li>{{ trans('website.represent_terms_and_conditions');}}</li>
    <li>{{ trans('website.accept_terms_and_conditions');}}</li>
  </ol>
    {{ trans('website.if_you_do_not_terms_and_conditions');}}</p>

<h4>{{ trans('website.terms');}}:</h4>
<p>
  <ol>
    <li>{{ trans('website.in_order_terms');}}</li>

    <li>{{ trans('website.the_design_terms');}}</li>
  </ol>
</p>

<h4>{{ trans('website.Conditions');}}:</h4>
<p>
  <ol class="inner-count">
    <li>{{ trans('website.you_agree_Conditions');}}
      <ol class="inner-count">
        <li>{{ trans('website.errors_Conditions');}}</li>
        <li>{{ trans('website.personal_Conditions');}}</li>
        <li>{{ trans('website.any_unauthorised_Conditions');}}</li>
        <li>{{ trans('website.any_interruption_Conditions');}}</li>
        <li>{{ trans('website.any_bugs_Conditions');}}</li>
        <li>{{ trans('website.any_errors_Conditions');}}</li>
        <li>{{ trans('website.automated_Conditions');}}</li>
      </ol>
    </li>
  </ol>
</p>
@stop