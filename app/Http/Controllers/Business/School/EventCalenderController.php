<?php

namespace App\Http\Controllers\Business\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\CreateEventCalenderRequest;
use App\Http\Requests\Business\School\UpdateEventCalenderRequest;
use App\Models\SchoolEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventCalenderController extends Controller
{
    /**
     * Normalize event type to match enum values
     */
    private function normalizeEventType($type)
    {
        if (empty($type)) {
            return 'etc';
        }

        $type = strtolower(trim($type));

        // Map various possible values to enum values
        $typeMapping = [
            'competitions' => 'competitions',
            'competition' => 'competitions',
            'soutěže' => 'competitions',
            'guest_lectures' => 'guest_lectures',
            'guest lectures' => 'guest_lectures',
            'přednášky hostů' => 'guest_lectures',
            'open_days' => 'open_days',
            'open days' => 'open_days',
            'otevírací dny' => 'open_days',
            'closing_days' => 'closing_days',
            'closing days' => 'closing_days',
            'uzavírací dny' => 'closing_days',
            'etc' => 'etc',
            'other' => 'etc',
            'a další' => 'etc',
        ];

        return $typeMapping[$type] ?? 'etc';
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user=auth()->user();
            $school = $user->school;

            $visited_pages = collect(json_decode($school->visited_pages));
            $is_visited = $visited_pages->contains('events');

            if(!$is_visited){
                $visited_pages->add('events');
                $school->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            if ($request->ajax()) {
                $start_date = Carbon::parse($request->start);
                $end_date = Carbon::parse($request->end);
                $user=auth()->user();
                $events = $user->school
                            ->eventCalenders()
                            ->where(function ($query) use ($start_date, $end_date) {
                                $query->where('start_date', '<=', $end_date)
                                    ->where('end_date', '>=', $start_date);
                            })
                            ->latest()
                            ->get();
                info('Event data for this school is ', ['events' => $events]);
                $formattedEvents = $events->map(function ($event) {
                    // Normalize event type to match enum values
                    $normalizedType = $this->normalizeEventType($event->type);

                    return [
                        'id' => $event->id,
                        'title' => $event->title ?? 'No Title',
                        'start' => $event->start_date->format('Y-m-d\TH:i'),
                        'end' => $event->end_date->format('Y-m-d\TH:i'),
                        'allDay' => false,
                        'extendedProps' => [
                            'calendar' => $normalizedType,
                            'name' => __(ucwords(str_replace('_', ' ', $normalizedType))) ?? '',
                            'description' => $event->description ?? '',
                        ],
                    ];
                });

                return response()->json($formattedEvents);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch events'], 500);
        }

        return view('content.business.school.event-calenders.index', compact('is_visited'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEventCalenderRequest $request)
    {
        try {
            $data = $request->validated();
            $school = auth()->user()->school;
            $event = $school->eventCalenders()->create($data);

            return response()->json([
                'success' => true,
                'message' => __('Event created successfully!'),
                'event' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to create event')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventCalenderRequest $request, SchoolEvent $event_calender)
    {
        try {
            $data=$request->validated();
            $event_calender->update($data);
            return response()->json([
                'success' => true,
                'message' => __('Event updated successfully!'),
                'event' => $event_calender
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update event')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolEvent $event_calender)
    {
        try {
            $event_calender->delete();
            return response()->json([
                'success' => true,
                'message' => __('Event deleted successfully!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete event')
            ], 500);
        }
    }
}
