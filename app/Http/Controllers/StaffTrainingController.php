<?php

namespace App\Http\Controllers;

use App\Staff;
use Illuminate\Support\Facades\Auth;

class StaffTrainingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(int $offset = 0)
    {
        //
        $user = Auth::guard('staff')->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $trainings_collection = $user->training()->with('frequency');
        $training_count = $trainings_collection->count();
        $trainings = $trainings_collection->skip($offset)->take(15)->orderBy('name')->get();

        if (!$trainings)
            return response()->json("trainings not found", 404);

        return response()->json(compact('trainings', 'training_count'), 200);
    }

    //
}
