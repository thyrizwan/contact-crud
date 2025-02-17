<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Exception;
use Illuminate\Http\Request;
use SimpleXMLElement;

class ContactApiController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::orderBy('name')->get();

        if ($contacts->isEmpty()) {
            $this->sendError(message: 'No contacts found', errors: [], status: 404);
        }

        return $this->sendResponse(data: $contacts, message: 'Contacts retrieved successfully');
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
    public function store(Request $request)
    {
        $ValidatedData = $this->validateContact($request);
        $contact = Contact::create($ValidatedData);

        return $this->sendResponse(data: $contact, message: 'Contact added successfully.', status: 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return $this->sendError(message: 'Contact not found', errors: [], status: 404);
        }

        return $this->sendResponse(data: $contact, status: 200);
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
    public function update(Request $request, int $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return $this->sendError(message: 'Contact not found', errors: [], status: 404);
        }

        $ValidatedData = $this->validateContact($request, $id);
        $contact->update($ValidatedData);

        return $this->sendResponse(data: $contact, message: 'Contact updated successfully.', status: 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return $this->sendError(message: 'Contact not found', errors: [], status: 404);
        }

        $contact->delete();
        return $this->sendResponse(data: $contact, message: 'Contact deleted successfully.', status: 200);
    }


    public function importXML(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xml',
        ]);

        $xmlFile = $request->file('file');
        $xmlContents = file_get_contents($xmlFile);
        $xmlData = new SimpleXMLElement($xmlContents);

        $totralImported = 0;
        $totalSkipped = 0;
        $skippedRecords = [];
        $domains = ['mail.com', 'gmail.com', 'outlook.com', 'tus.com', 'ril.com', 'google.com'];

        foreach ($xmlData->contact as $contact) {
            $name = isset($contact->name) ? (string) $contact->name : null;
            $phone = isset($contact->phone) ? (string) $contact->phone : null;
            $dial_code = isset($contact->dial_code) ? (string) $contact->dial_code : null;

            if (!isset($contact->email) || empty((string) $contact->email)) {
                $email = strtolower(str_replace(' ', '', $name)) . substr($phone, -4) . '@' . $domains[array_rand($domains)];
            } else {
                $email = (string) $contact->email;
            }

            if (
                empty($name) || empty($phone) || empty($email) ||
                Contact::where('phone', $phone)->orWhere('email', $email)->exists()
            ) {
                $totalSkipped++;
                $skippedRecords[] = [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'reason' => empty($name) ? 'Missing name' : (empty($phone) ? 'Missing phone' : (empty($email) ? 'Missing email' : 'Duplicate phone or email')),
                ];
                continue;
            }

            Contact::create([
                'name' => $name,
                'phone' => $phone,
                'dial_code' => $dial_code,
                'email' => $email
            ]);

            $totralImported++;
        }

        return $this->sendResponse(
            data: [
                'imported' => $totralImported,
                'skipped' => $totalSkipped,
                'skipped_records' => $skippedRecords,
            ],
            message: 'Contacts imported successfully.',
            status: 200
        );
    }
}
