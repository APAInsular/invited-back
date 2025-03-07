<?php
namespace App\Http\Controllers;

use App\Models\Attendant;
use Illuminate\Http\Request;

class AttendantController extends Controller
{
    public function index()
    {
        return response()->json(Attendant::with('guest')->get(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:400',
            'firstSurname' => 'required|string|max:400',
            'secondSurname' => 'nullable|string|max:400',
            'age' => 'required|integer|min:0',
            'guest_id' => 'required|exists:guests,id'
        ]);

        $attendant = Attendant::create($request->all());

        return response()->json($attendant, 201);
    }

    public function show($id)
    {
        $attendant = Attendant::with('guest')->find($id);

        if (!$attendant) {
            return response()->json(['message' => 'Attendant not found'], 404);
        }

        return response()->json($attendant, 200);
    }

    public function update(Request $request, $id)
    {
        $attendant = Attendant::find($id);

        if (!$attendant) {
            return response()->json(['message' => 'Attendant not found'], 404);
        }

        $attendant->update($request->all());

        return response()->json($attendant, 200);
    }

    public function destroy($id)
    {
        $attendant = Attendant::find($id);

        if (!$attendant) {
            return response()->json(['message' => 'Attendant not found'], 404);
        }

        $attendant->delete();

        return response()->json(['message' => 'Attendant deleted'], 200);
    }
}
