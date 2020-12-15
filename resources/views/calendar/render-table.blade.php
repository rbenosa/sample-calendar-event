<div class="d-flex justify-content-center mt-5 hide event-loader">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<table class="table events-table">
    <thead>
        <tr>
            <th scope="col" style="width: 20%;">
                <h4 id="month_year">{{ $current_month }} {{ $current_year }}</h4>
            </th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($days_arr as $items)

        <tr class="{{ ( null != $items['event'] ) ? 'table-success' : ''  }}">
            <td>{{ $items['dt'] }}</td>
            <td>{{ $items['event'] }}</td>
        </tr>
        
        @endforeach
    </tbody>
</table>