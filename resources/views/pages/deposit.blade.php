@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">Deposit List</div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($deposit_list as $key => $value)
                                    <tr>
                                        <td>
                                            {{$key+1}}
                                        </td>
                                        <td>
                                            {{$value->date}}
                                        </td>
                                        <td>
                                            {{$value->amount}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>


                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Deposit Amount</div>
                        <div class="card-body">
                            <form action="{{route('deposit_store')}}" method="post" class="form-horizontal"
                                  enctype="multipart/form-data">

                                <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">Amount</label>

                                    <div class="col-md-6">
                                        <input id="amount" type="text" class="form-control" name="amount" required
                                               autocomplete="off" autofocus oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                                <div class="row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Deposit
                                        </button>
                                    </div>
                                </div>

                                @csrf
                            </form>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
@endsection
