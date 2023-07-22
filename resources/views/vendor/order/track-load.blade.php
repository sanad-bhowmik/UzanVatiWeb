                                            <tr>
                                                <th>{{ __("Title") }}</th>
                                                <th>{{ __("Details") }}</th>
                                                <th>{{ __("Date") }}</th>
                                                <th>{{ __("Time") }}</th>
                                            
                                            </tr>
                                            @foreach($order->tracks as $track)

                                            <tr data-id="{{ $track->id }}">
                                                <td width="30%">
                                                    @if (ucwords($track->title) == 'Confirmed')
                                                    {{ __('Picked') }}
                                                @elseif(ucwords($track->title) == 'Declined')
                                                    {{ __('Canceled') }}
                                                    @elseif(ucwords($track->title) == 'Completed')
                                                    {{ __('Delivered') }}
                                                @else
                                                    {{ ucwords($track->title) }}
                                                @endif



                                                </td>
                                                <td width="30%">{{ $track->text }}</td>
                                                <td>{{  date('Y-m-d',strtotime($track->created_at)) }}</td>
                                                <td>{{  date('h:i:s:a',strtotime($track->created_at)) }}</td>
                                                
                                            </tr>
                                            @endforeach