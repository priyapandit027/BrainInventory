<?php

namespace App\Http\Controllers;

use App\HolidayDestination;
use App\UserRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class DestinationController extends Controller
{
    public function lists(Request $request)
    {
        try {
            if ($request->ajax()) {
                $requestData = $request->all();
                // $selectBrandId = $requestData['selectBrandId'];
                $rows = array();
                $params = $columns = $totalRecords = array();
                $params = $request;

                $columns = array(
                    0 => 'holiday_destination.id',
                    1 => 'holiday_destination.place_name',
                    2 => 'user_rating.rating',
                    3 => 'user_rating.review',
                    4 => 'holiday_destination.id',
                );
                $destinationData = HolidayDestination::select('holiday_destination.id', 'holiday_destination.place_name', 'user_rating.rating', 'user_rating.review')->join('user_rating', 'user_rating.destination_id', '=', 'holiday_destination.id')->join('users', 'users.id', '=', 'user_rating.user_id');

                if (!empty($request->input('search.value'))) {
                    $search = $request->input('search.value');
                    $destinationData->where(function ($destinationData) use ($search) {
                        $destinationData->where('holiday_destination.place_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('user_rating.rating', 'LIKE', '%' . $search . '%')
                            ->orWhere('user_rating.review', 'LIKE', '%' . $search . '%');
                    });
                }
                $totalRecords = $destinationData->get()->count();
                $totalFiltered = $destinationData->get()->count();
                $destinations = $destinationData->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])->take($params['length'])->skip($params['start'])->get();
                $url = URL::to("/");
                $class = '';
                if (!empty($destinations)) {

                    foreach ($destinations as $row) {
                        $rating = $this->displayRating($row->rating,$row->id);
                        $action = '<a href="#" class="updateRating" data-toggle="modal" id="' . $row->id . '" data-target="#ratingModal">Update</a>';

                        $row = array(
                            '<td class="control"></td>',
                            ucfirst($row->place_name),
                            $rating,
                            ucfirst($row->review),
                            $action,

                        );
                        $rows[] = $row;
                    }
                }
                $list['draw'] = intval($params['draw']);
                $list['recordsTotal'] = intval($totalRecords);
                $list['recordsFiltered'] = intval($totalFiltered);
                $list['aaData'] = $rows;

                return json_encode($list);
            }
        } catch (Exception $e) {
            $response = array('status' => 'error', 'message' => trans('messages.pleaseTryAgain'));
            return response()->json($response);
        }
    }

    private function displayRating($rating,$destinationId)
    {
        $html = '';
        if ($rating == 5) {
            $html = '<input type="hidden" id="ratingValue'.$destinationId.'" value="'.$rating.'"><span class="fa fa-star checked"></span>
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star checked"></span>';
        } else if ($rating == 4) {
            $html = '<input type="hidden" id="ratingValue'.$destinationId.'" value="'.$rating.'"><span class="fa fa-star checked"></span>
            <span class="fa fa-star checked"></span>
            <span class="fa fa-star checked"></span>
            <span class="fa fa-star checked"></span>
            <span class="fa fa-star"></span>';
        } else if ($rating == 3) {
            $html = '<input type="hidden" id="ratingValue'.$destinationId.'" value="'.$rating.'"><span class="fa fa-star checked"></span>
                <span class="fa fa-star checked"></span>
                <span class="fa fa-star checked"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>';
        } else if ($rating == 2) {
            $html = '<input type="hidden" id="ratingValue'.$destinationId.'" value="'.$rating.'"><span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
        } else if ($rating == 1) {
            $html = '<input type="hidden" id="ratingValue'.$destinationId.'" value="'.$rating.'"><span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
        }
        return $html;
    }

    public function updateRating(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = array();
                parse_str($request['formValue'], $data);

                $destinationId = $data['hidDestinationId'];
                $rating = $data['rating'];
                $review = $data['review'];

                $userId = Auth::user()->id;
                $userRating = UserRating::where('destination_id', $destinationId)->where('user_id', $userId)->first();
                if (!empty($userRating)) {
                    $userRating->rating = $rating;
                    $userRating->review = $review;
                    $userRating->save();
                }
                $response = array('status' => 'success', 'message' => 'Updated successfully');
                return response()->json($response);
            }
        } catch (Exception $e) {
            $response = array('status' => 'error', 'message' => trans('messages.pleaseTryAgain'));
            return response()->json($response);
        }
    }

}
