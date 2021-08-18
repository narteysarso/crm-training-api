<?php

namespace App\Http\Controllers;

use App\Training;
use App\Staff;
use App\Frequency;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;

class AdminTrainingController extends Controller
{
    protected $jwt;
    private static $searchword;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        //
        $this->jwt = $jwt;
    }

    public function index(int $offset = 0)
    {
        //
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $trainings_collection = Training::with('frequency')->where("company_id", $company->id);
        $training_count = $trainings_collection->count();
        $trainings = $trainings_collection->skip($offset)->take(15)->orderBy('name')->get();

        if (!$trainings)
            return response()->json("trainings not found", 404);

        return response()->json(compact('trainings', 'training_count'), 200);
    }

    public function show(int $id)
    {
        //

        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $training = Training::where('id', $id)->where("company_id", $company->id)->first();

        if (!$training)
            return response()->json("trainings not found", 404);

        $training->frequency;

        return response()->json(compact('training'), 200);

    }

    public function create(Request $request)
    {
        //
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $this->validate($request, [
            'name' => "string|required|unique:trainings",

            'frequency_id' => "integer|required|exists:frequencies,id",
        ]);

        $credentials = $request->all();
        $credentials['company_id'] = $company->id;
        $training = Training::create($credentials);

        if (!$training)
            return response()->json("Unable to create training", 500);

        $training->frequency;

        return response()->json(compact('training'), 200);

    }

    public function edit(Request $request)
    {
        //
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $training = Training::where('id', $request->id)->where("company_id", $company->id)->first();

        if (!$training)
            return response()->json("trainings not found", 404);

        $validationRule = [];

        if ($request->has('name') && ($request->name === $training->name))
            $validationRule['name'] = "string|required";
        else
            $validationRule['name'] = "string|required|unique:trainings";

        $validationRule['frequency_id'] = "integer|required|exists:frequencies,id";
        $validationRule['description'] = "string";

        $this->validate($request, $validationRule);

        $result = $training->update($request->all());

        if (!$result)
            return response()->json('unable to update training', 500);

        $training->frequency;

        return response()->json(compact('training'), 200);

    }

    public function delete(Request $request)
    {
        //
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $training = Training::where('id', $request->id)->where("company_id", $company->id)->first();

        if (!$training)
            return response()->json("training not found", 404);

        $result = $training->delete();

        if (!$result)
            return response()->json('unable to delete training', 500);

        return response()->json("training deleted successfully", 200);
    }

    public function frequency()
    {
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $frequencies = Frequency::all();

        if (!$frequencies)
            return response()->json("trainings not found", 404);

        return response()->json(compact('frequencies'), 200);

    }

    public function trainee(int $id, int $offset = 0)
    {
         //
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);


        $training = Training::where('id', $id)->where("company_id", $company->id)->first();

        if (!$training)
            return response()->json("training not found", 404);

        $trainee_collection = $training->staffs()->with(['jobs' => function ($query) {
            $query->with('group', 'role', 'branch')->orderBy('effective_date', 'desc')->take(1);
        }]);
        $trainee_count = $trainee_collection->count();
        $trainees = $trainee_collection->skip($offset)->take(20)->get();

        if (!$trainees)
            return response()->json('no trainee found', 404);

        return response()->json(compact('trainees', 'trainee_count'), 200);
    }

    public function search(Request $request)
    {
        //
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        if (!is_null($request->searchword))
            $searchquery = Training::where('company_id', $company->id)
            ->where('trainings.name', 'LIKE', "%{$request->searchword}%")
            ->orWhere('trainings.description', 'LIKE', "%{$request->searchword}%")
            ->orWhere('trainings.link', 'LIKE', "%{$request->searchword}%")
            ->orWhere('frequencies.name', 'LIKE', "%{$request->searchword}%")
            ->join('frequencies', 'frequencies.id', '=', 'trainings.frequency_id')
            ->select('trainings.name', 'trainings.id', 'trainings.link', 'trainings.description', 'trainings.frequency_id')
            ->with('frequency')
            ->orderBy('trainings.name')
            ->get();
        else {
            $searchquery = Training::with('frequency')->where('company_id', $company->id)->get();
        }

        $trainings = $searchquery;


        if (!$trainings)
            return response()->json('no match found', 404);

        return response()->json(compact('trainings'), 200);
    }

    public function searchTrainee(Request $request, int $id)
    {
        //
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        $training = Training::where('id', $id)->where("company_id", $company->id)->first();

        self::$searchword = $request->searchword;

        if (!$training)
            return response()->json("training not found", 404);


        if (!is_null($request->searchword)) {
            $searchquery = Staff::with(['jobs' => function ($query) {
                $query->with('group', 'role', 'branch')->orderBy('effective_date', 'desc')->take(1);
            }])
                ->where('staff_training.training_id', $id)
                ->where(function ($query) {
                    $query->where('staffs.firstname', 'LIKE', "%" . self::$searchword . "%")
                        ->orWhere('staffs.lastname', 'LIKE', "%" . self::$searchword . "%")
                        ->orWhere('staffs.email', 'LIKE', "%" . self::$searchword . "%")
                        ->orWhere('staffs.mobile', 'LIKE', "%" . self::$searchword . "%");
                })
                ->join('staff_training', 'staffs.id', '=', 'staff_id')
                ->get();
        } else {
            $searchquery = $training->staffs()->with(['jobs' => function ($query) {
                $query->with('group', 'role', 'branch')->orderBy('effective_date', 'desc')->take(1);
            }])->get();
        }

        $trainees = $searchquery;


        if (!$trainees)
            return response()->json('no match found', 404);

        return response()->json(compact('trainees'), 200);
    }

    public function addTrainee(Request $request)
    {
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        // dd($request);
        // return;
        $this->validate($request, [
            'trainingid' => 'required|integer|exists:trainings,id',
            'trainees' => 'required|array'
        ]);

        $training = Training::where('id', $request->trainingid)->where("company_id", $company->id)->first();

        if (!$training)
            return response()->json("training not found", 404);

        $trainees = array();
        $failure_count = 0;
        foreach ($request->trainees as $traineeid) {
            # code...
            $staff = Staff::with(['jobs' => function ($query) {
                $query->with('group', 'role', 'branch')->orderBy('effective_date', 'desc')->take(1);
            }])->where('id', $traineeid)->where('company_id', $company->id)->first();
            if (!$staff) {
                $failure_count++;
                return;
            }
            $training->staffs()->detach($staff->id);
            $training->staffs()->attach($staff->id);
            array_push($trainees, $staff);
        }

        if ($failure_count > 0)
            return response()->json("failed to add {$failure_count} trainee(s) to training session", 505);

        return response()->json(compact('trainees'), 200);

    }

    public function deleteTrainee(Request $request)
    {
        $user = $this->jwt->user();

        if (!$user)
            return response()->json('user not found', 404);

        $company = $user->company;
        if (!$company)
            return response()->json('company not found', 404);

        // dd($request);
        // return;
        $this->validate($request, [
            'trainingid' => 'required|integer|exists:trainings,id',
            'traineeid' => 'required|integer|exists:staffs,id'
        ]);

        $training = Training::where('id', $request->trainingid)->where("company_id", $company->id)->first();

        if (!$training)
            return response()->json("training not found", 404);

        $training->staffs()->detach($request->traineeid);

        return response()->json("trainee removed", 200);

    }

}
