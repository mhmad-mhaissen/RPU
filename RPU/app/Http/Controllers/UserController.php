<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\Specialization;
use App\Models\Specializations_Per_University;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUniversities()
    {
        $universities = University::all();
        return response()->json([
            'universities' => $universities
        ], 200);
    }

    public function getSpecializationsForUniversity($universityId)
    {
        $university = University::find($universityId);

        if (!$university) {
            return response()->json([
                'message' => 'University not found'
            ], 404); // 404 - Not Found
        }

        $university = University::with([
            'specializationsPerUniversity.specialization', 
            'specializationsPerUniversity.grant'
        ])
        ->findOrFail($universityId);
    
        $response = [
            'university' => $university->name,
            'specializations' => [],
            'grants' => [],
        ];
    
        foreach ($university->specializationsPerUniversity as $specializationPerUniversity) {
            $specialization = $specializationPerUniversity->specialization;
            $response['specializations'][] = [
                'specialization_id'=>$specialization->id,
                'specialization_name' => $specialization->name,
                'specialization_details' => $specialization->details,
                'price_per_hour' => $specializationPerUniversity->price_per_hour,
                'num_seats' => $specializationPerUniversity->num_seats,
            ];
    
            if ($specializationPerUniversity->grant) {
                $response['grants'][] = [
                    'specialization_id'=>$specialization->id,
                    'specialization_name' => $specialization->name,
                    'specialization_details' => $specialization->details,
                    'num_seats' => $specializationPerUniversity->num_seats,
                    'grant_num_seats' => $specializationPerUniversity->grant->num_seats,
                ];
            }
        }
    
        return response()->json($response);
    }
    // public function submitRequest(Request $request)
    // {
    //     $validated = $request->validate([
    //         'unis_id' => 'required|exists:specializations__per__universities,id',
    //         'r_type_id' => 'required|exists:r_types,id',
    //         'certificate_country' => 'required|string',
    //         'total' => 'required|numeric',
    //         'personal_id' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048', // صورة الهوية
    //         'Bachelors_certificate' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048', // صورة شهادة البكالوريا
    //     ]);

    //     $user = $request->user();
    //     if (!$user->default_payment_method) {
    //         return response()->json([
    //             'error' => 'User does not have a default payment method',
    //         ], 400); // Bad Request
    //     }

    //     $payment = $user->payment()->create([
    //         'payment_method_id' => $user->default_payment_method,
    //         'amount' => $validated['total'],
    //         'payment_status' => 'pending', // يمكن تحديثه لاحقًا
    //         'currency' => 'USD', // يمكن تغييره حسب المتطلبات
    //         'transaction_id' => uniqid('txn_'),
    //         'payment_date' => now(),
    //     ]);

    //     $personalIdPath = $request->file('personal_id')->store('documents/personal_ids', 'public');
    //     $bachelorsCertificatePath = $request->file('Bachelors_certificate')->store('documents/Bachelors_certificates', 'public');

    //     $user->document()->create([
    //         'document_type' => 'personal_id',
    //         'image' => $personalIdPath,
    //     ]);

    //     $user->document()->create([
    //         'document_type' => 'Bachelors_certificate',
    //         'image' => $bachelorsCertificatePath,
    //     ]);

    //     $newRequest = RequestModel::create([
    //         'payment_id' => $payment->id,
    //         'unis_id' => $validated['unis_id'],
    //         'r_type_id' => $validated['r_type_id'],
    //         'request_status' => 'pending',
    //         'certificate_country' => $validated['certificate_country'],
    //         'total' => $validated['total'],
    //     ]);

    //     return response()->json([
    //         'message' => 'Request submitted successfully',
    //         'request' => $newRequest,
    //     ], 201); // Created
    // }

}
