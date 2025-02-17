<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use SimpleXMLElement;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::orderBy('name')->get();

        if ($contacts->isEmpty()) {
            return view('contacts.index')->with('error', 'No contacts found. Please add a contact.');
        }

        return view('contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateContact($request);

        Contact::create($validatedData);

        return redirect()->route('contacts.index')->with('success', 'Contact added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return redirect()->route('contacts.index')->with('error', 'Contact not found.');
        }

        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return redirect()->route('contacts.index')->with('error', 'Contact not found.');
        }

        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $contact = Contact::find($id);
        
        if (!$contact) {
            return redirect()->route('contacts.index')->with('error', 'Contact not found.');
        }
        
        $validatedData = $this->validateContact($request, $id);
        $contact->update($validatedData);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return redirect()->route('contacts.index')->with('error', 'Contact not found.');
        }

        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    /**
     * Import contacts from XML file.
     */
    public function importXML(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xml',
        ]);

        $xmlFile = $request->file('file');
        $xmlContent = file_get_contents($xmlFile);
        $xmlData = new SimpleXMLElement($xmlContent);

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

        if ($totralImported > 0) {
            session()->flash('success', "$totralImported contacts imported successfully.");
        }

        if ($totalSkipped > 0) {
            session()->flash('warning', "$totalSkipped contacts were skipped.");
            session()->flash('skipped_records', $skippedRecords);
        }

        return redirect()->back();
    }
}
