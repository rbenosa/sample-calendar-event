<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Calendar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.13.0/dist/sweetalert2.min.css">
    
    <style>
        .hide {
            display: none !important;
        }

        .day_err_msg {
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;
            color: #dc3545;
        }
    </style>

</head>

<body>

    <div class="container mt-5">

        <div class="row">

            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        Calendar
                    </div>
                    <div class="card-body">

                        <div class="row">

                            <div class="col-5">
                                <form id="event-form">
                                    @csrf
                                    <!-- Event input [start] -->
                                    <div class="mb-3 event-wrapper">
                                        <label for="event-title" class="form-label">Event</label>
                                        <input type="text" name="event_title" class="form-control" id="event-title">
                                    </div>
                                    <!-- Event input [end] -->

                                    <!-- Date range input [start] -->
                                    <div class="mb-3">

                                        <div class="row">
                                            <div class="col from-wrapper">
                                                <label for="from" class="form-label">From</label>
                                                <input type="date" name="from" class="form-control" id="from" aria-label="From">
                                            </div>

                                            <div class="col to-wrapper">
                                                <label for="to" class="form-label">To</label>
                                                <input type="date" name="to" class="form-control" id="to" aria-label="To">
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Date range input [end] -->

                                    <!-- Dates input [start] -->
                                    <div class="mb-3 days-wrapper">

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="day[]" type="checkbox" id="mon" value="Mon">
                                            <label class="form-check-label" for="mon">Mon</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="day[]" type="checkbox" id="tue" value="Tue">
                                            <label class="form-check-label" for="tue">Tue</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="day[]" type="checkbox" id="wed" value="Wed">
                                            <label class="form-check-label" for="wed">Wed</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="day[]" type="checkbox" id="thu" value="Thu">
                                            <label class="form-check-label" for="thu">Thu</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="day[]" type="checkbox" id="fri" value="Fri">
                                            <label class="form-check-label" for="fri">Fri</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="day[]" type="checkbox" id="sat" value="Sat">
                                            <label class="form-check-label" for="sat">Sat</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="day[]" type="checkbox" id="sun" value="Sun">
                                            <label class="form-check-label" for="sun">Sun</label>
                                        </div>

                                    </div>
                                    <!-- Dates input [end] -->

                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary" id="btn-submit">Save</button>
                                    </div>
                                </form>

                            </div>


                            <div class="col-7">
                                @include('calendar.render-table')
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {

            var xhr = null;

            $('#event-form').submit(function(e) {

                e.preventDefault();

                var formData = new FormData(this);
                var frm_btn = '#btn-submit';

                // $('.event-loader').removeClass('hide');
                // $('.events-table').addClass('hide');


                xhr = $.ajax({
                    url: "{{ route('store-event') }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: formData,
                    processData: false,
                    contentType: false,

                    beforeSend: function() {
                        $(frm_btn).attr('disabled', 'disabled');
                        $(frm_btn).empty();
                        $(frm_btn).append('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span class="visually-hidden">Loading...</span>');

                        $('.event_err_msg, .from_err_msg, .to_err_msg, .day_err_msg').remove();
                        $('#event-title, #from, #to, #mon, #tue, #wed, #thu, #fri, #sat, #sun').removeClass('is-invalid');

                        if (xhr != null) {
                            xhr.abort();
                        }
                    },

                    success: function(data) {
                        $(frm_btn).removeAttr('disabled');
                        $(frm_btn).empty();
                        $(frm_btn).text('Save');

                        $('.event-loader').addClass('hide');
                        $('.events-table').removeClass('hide').html(data);
                        $('#event-title, #from, #to').removeClass('is-invalid');

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Event successfully saved'
                        })

                    },

                    error: function(data) {
                        $(frm_btn).removeAttr('disabled');
                        $(frm_btn).empty();
                        $(frm_btn).text('Save');

                        $('.event-loader').addClass('hide');
                        $('.events-table').removeClass('hide');

                        error = data.responseJSON;

                        $.each(error.errors, function(k, v) {

                            if (k == 'event_title') {
                                let event_err_msg = '<div class="invalid-feedback event_err_msg">' + v + '</div>';
                                $('#event-title').addClass('is-invalid')
                                $('.event-wrapper').append(event_err_msg);
                            } else if (k == 'from') {
                                let from_err_msg = '<div class="invalid-feedback from_err_msg">' + v + '</div>';
                                $('#from').addClass('is-invalid')
                                $('.from-wrapper').append(from_err_msg);
                            } else if (k == 'to') {
                                let to_err_msg = '<div class="invalid-feedback to_err_msg">' + v + '</div>';
                                $('#to').addClass('is-invalid')
                                $('.to-wrapper').append(to_err_msg);
                            } else if (k == 'day') {
                                console.log(v)
                                let day_err_msg = '<div class="day_err_msg">' + v + '</div>';
                                $('#mon, #tue, #wed, #thu, #fri, #sat, #sun').addClass('is-invalid')
                                $('.days-wrapper').append(day_err_msg);
                            } else {}

                        })

                    }


                })

            });

        })
    </script>


</body>

</html>