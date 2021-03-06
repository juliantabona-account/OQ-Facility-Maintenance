@extends('layouts.app') @section('style')
<style>
    .jobcard-container .card {
        border: 1px solid #dbe3e6;
    }

    .lower-font {
        font-size: 14px;
    }

    .reference-details span {
        padding-top: 3px;
        display: block;
    }
</style>
@endsection @section('content')
    <div class="row jobcard-container">
        <div class="col-lg-12 d-flex flex-column">
            <div class="row flex-grow">
                <div class="col-12 col-md-8 col-lg-8 grid-margin stretch-card">
                    <div class="card card-hoverable">
                        <div class="card-body p-3 pt-4">
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('jobcard-create') }}?company_id={{ $client->id }}?type=client" class="btn btn-primary btn-sm float-right mb-2">
                                        <i class="icon-flag icons ml-1"></i>
                                        Create Jobcard
                                    </a>
                                </div>
                                <div class="col-12">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item text-success">{{ $jobcards->total() }} Total {{ $jobcards->total() == 1 ? 'Job':'Jobs' }}</li>
                                            @foreach($jobcardProcessSteps as $processStep)
                                                @php
                                                    $totalJobsAssigned = $processStep->jobcards()->where('client_id', $client->id)->count();
                                                @endphp
                                                @if( $totalJobsAssigned != 0 )
                                                    <li class="breadcrumb-item text-black"><a href="#">{{ $totalJobsAssigned }} {{ $processStep->step_instruction['name'] }}</a></li>
                                                @else
                                                    <li class="breadcrumb-item text-black">{{ $totalJobsAssigned }} {{ $processStep->step_instruction['name'] }}</li>
                                                @endif
                                            @endforeach
                                        </ol>
                                    </nav>
                                </div>
                                <div class="col-12">
                                    <h6 class="mt-2 mb-0 ml-2">Client Projects</h6>
                                    <div class="table-responsive table-hover">
                                        <table class="table mt-3 border-top">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%">Job</th>
                                                    <th style="width: 14%">Due</th>
                                                    <th style="width: 18%">Status</th>
                                                    <th style="width: 15%">Priority</th>
                                                    <th style="width: 15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jobcards as $jobcard)
                                                    <tr class='clickable-row' data-href='/jobcards/{{ $jobcard->id }}'>
                                                        <td data-toggle="tooltip" data-placement="top" title="{{ $jobcard->description }}">{{ $jobcard->title ? $jobcard->title:'____' }}</td>    
                                                        <td class="d-none d-md-table-cell">
                                                            @php
                                                                $deadline = round((strtotime($jobcard->end_date)  
                                                                                - strtotime(\Carbon\Carbon::now()->toDateTimeString()))  
                                                                                / (60 * 60 * 24)) 
                                                            @endphp
                                                            @if($deadline > 0)
                                                                {{ $deadline == 1 ? $deadline.' day' : $deadline.' days'  }}
                                                            @else
                                                                <i class="icon-exclamation icons mr-1 text-danger"></i>
                                                                <span class="text-danger">due</span>
                                                            @endif
                                                        </td>
                                                                                                
                                                        <td class="d-none d-md-table-cell">
                                                            @if($jobcard->processFormStep)  
                                                                @php
                                                                    $status = $jobcard->processFormStep->step_instruction;
                                                                @endphp
                                                                @if($status)
                                                                    <span>{{ $status ? $status['name'] : '____' }}</span>
                                                                @else
                                                                    ____
                                                                @endif 
                                                            @else
                                                                ____
                                                            @endif 
                                                        </td>  
                                                        
                                                        <td class="d-none d-md-table-cell">
                                                            @if($jobcard->priority)
                                                                <div  data-toggle="tooltip" data-placement="top" title="{{ $jobcard->priority->description }}"
                                                                    class="badge badge-success" style="min-width: 80px;background:{{ $jobcard->priority->color_code }};">{{ $jobcard->priority ? $jobcard->priority->name:'____' }}</div>
                                                            @else
                                                                ____
                                                            @endif    
                                                        </td>  
                                                        <td>
                                                            <a class="btn btn-primary p-1 pl-4 pr-4 not-clickable" href="{{ route('jobcard-download-pdf', [$jobcard->id]) }}" target="_blank" class="btn btn-primary">
                                                                <i class="icon-cloud-download icons"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-column flex-sm-row mt-4">
                                        @if($jobcards->total() != 0)
                                            <p class="mb-3 ml-3 mb-sm-0">
                                                <strong>{{ $jobcards->total() }}</strong>{{ $jobcards->total() == 1 ? ' result': '  results' }} found
                                            </p>
                                        @else
                                            <div class="col-6 offset-3" data-toggle="tooltip" data-placement="top" title="Create a new jobcard for {{ $client->name }}">
                                                <a href="{{ route('jobcard-create') }}?company_id={{ $client->id }}?type=client" class="btn btn-success p-5 w-100 animated-strips">                                            
                                                    <i class="d-block icon-sm icon-flag icons" style="font-size: 25px;"></i>
                                                    <span class="d-block mt-4">Create Jobcard</span>
                                                </a>
                                            </div>
                                        @endif
                                        <nav>
                                            {{ $jobcards->links() }}
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-4 grid-margin stretch-scard">
                    <div class="card card-hoverable">
                        <div class="card-body p-3 pt-4">
                            <div class="row">
                                @if($client)
                                    <div class="col-12">
                                        <div class="bg-primary p-2 text-white">
                                            <i class="float-left icon-emotsmile icon-sm icons ml-3 mr-2"></i>
                                            <h6 class="card-title mb-0 ml-2 text-white">Client Details</h6>
                                        </div>
                                        <div class="mt-3 ml-3 reference-details">
                                            @if($client->logo_url)
                                                <div class="lightgallery">
                                                    <a href="{{ $client->logo_url }}">
                                                        <img class="company-logo img-thumbnail mb-2 p-2 rounded rounded-circle w-50" src="{{ $client->logo_url }}" />
                                                    </a>
                                                </div>
                                            @endif
                                            <span class="lower-font">
                                                <b>Client Name: </b>{{ $client->name ? $client->name:'____' }}<br/>
                                                <b>City/Town: </b>{{ $client->city ? $client->city:'____' }}<br/>
                                                <b>Address: </b>{{ $client->address ? $client->address:'____' }}
                                            </span>
                                            <br/>
                                            <span class="lower-font">
                                                <b>Phone: </b>
                                                {{ $client->phone_ext ? '+'.$client->phone_ext.'-':'___-' }}
                                                {{ $client->phone_num ? $client->phone_num:'____' }}
                                            </span>
                                            <span class="lower-font mb-3">
                                                <b>Email: </b>{{ $client->city ? $client->email:'____' }}
                                            </span>
                                            <span class="lower-font clearfix mb-3">
                                                <a href="{{ route('company-edit', $client->id) }}?type=client" style="font-size:  12px;" class="float-right mr-1"><i class="icon-pencil"></i> Edit</a>   
                                            </span> 
                                        </div>
                                    </div>

                                    @if($contacts->total())
                                        <div class="col-12 mb-2">
                                            <div class="bg-primary p-2 text-white">
                                                <i class="float-left icon-user icon-sm icons ml-3 mr-2"></i>
                                                <h6 class="card-title mb-0 ml-2 text-white d-inline">Contact Details ({{ $contacts->total() }})</h6>
                                                <a href="#" style="font-size:  12px;" class="float-right mr-1 mt-1 text-white"><i class="icon-eye"></i> View All</a>
                                            </div>
                                            
                                            @foreach($contacts as $contact)
                                                <div class="mt-1 ml-2 reference-details">
                                                    <div class=" d-flex align-items-center border-bottom p-2">
                                                        <a class="p-0 m-0">
                                                            <img class="img-sm rounded-circle" src="http://127.0.0.1:8000/images/profile_placeholder.svg" alt="">
                                                        </a>
                                                        <div class="wrapper w-100 ml-3">
                                                            <p class="pt-2 mb-2" style="font-size:  12px;">
                                                                <a href="#" class="mr-1">{{ $contact->first_name ? $contact->first_name:'____' }} {{ $contact->last_name ? $contact->last_name:'____' }}</a>
                                                            </p>
                                                            <div>
                                                                @if($contact->position)
                                                                    <div class="d-inline mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $contact->position ? $contact->position:'____' }}">
                                                                        <i class="icon-info text-dark"></i>
                                                                    </div>
                                                                @endif
                                                                @if($contact->phone_num)
                                                                    <div class="d-inline mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $contact->phone_ext ? '+'.$contact->phone_ext.'-':'___-' }} {{ $contact->phone_num ? $contact->phone_num:'____' }}">
                                                                        <i class="icon-phone text-dark"></i>
                                                                    </div>
                                                                @endif
                                                                @if($contact->email)
                                                                    <div class="d-inline" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $contact->email ? $contact->email:'____' }}">
                                                                        <i class="icon-envelope text-dark"></i>
                                                                    </div>
                                                                @endif
                                                                <a href="#" style="font-size:  12px;" class="float-right mr-1"><i class="icon-trash"></i> Remove</a>
                                                                <a href="#" style="font-size:  12px;" class="float-right mr-1"><i class="icon-pencil"></i> Edit</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="float-right d-flex align-items-center justify-content-between flex-column flex-sm-row mt-4">
                                                <nav>
                                                    {{ $contacts->links() }}
                                                </nav>
                                            </div>

                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div data-toggle="tooltip" data-placement="top" title="Add another contact/reference working at this company or organisation" >
                                            <button type="button" class="animated-strips btn btn-success float-right pt-3 pb-3 pl-4 pr-4 w-100" data-toggle="modal" data-target="#add-reference-modal">                                            
                                                <i class="icon-sm icon-user icons"></i>
                                                <span class="mt-4">Add Contact</span>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <div data-toggle="tooltip" data-placement="top" title="Add a company or organisation corresponding to this jobcard">
                                            <button type="button" class="btn btn-success p-5 w-100 animated-strips" data-toggle="modal" data-target="#add-client-modal">                                            
                                                <i class="d-block icon-sm icon-emotsmile icons" style="font-size: 25px;"></i>
                                                <span class="d-block mt-4">Add Client</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('jobcard.modals.add_contact')

<!-- content-wrapper ends -->

@endsection 

@section('js') 

@endsection