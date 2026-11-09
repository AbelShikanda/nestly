<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

class PagesController extends Controller
{
    public function agent()
    {
        $pageTitle = 'Our Staff';
        $breadcrumbLinks = [
            'DescriptionOne' => 'Meet our staff',
            'DescriptionTwo' => 'by the fine folks at',
            'url' => '/agent',
            'label' => 'staff',
        ];
        return view(
            'pages.agent',
            with([
                'pageTitle' => $pageTitle,
                'breadcrumbLinks' => $breadcrumbLinks,
            ]),
        );
    }

    public function blog()
    {
        $pageTitle = 'Our Blogs';
        $breadcrumbLinks = [
            'DescriptionOne' => 'Develop a',
            'DescriptionTwo' => 'for reading',
            'url' => '/blog',
            'label' => 'blog',
        ];
        return view(
            'pages.blog',
            with([
                'pageTitle' => $pageTitle,
                'breadcrumbLinks' => $breadcrumbLinks,
            ]),
        );
    }

    public function contact()
    {
        $pageTitle = 'Contact Us';
        $breadcrumbLinks = [
            'DescriptionOne' => 'We always',
            'DescriptionTwo' => 'to talk to our customers',
            'url' => '/contact',
            'label' => 'contact',
        ];
        return view(
            'pages.contact',
            with([
                'pageTitle' => $pageTitle,
                'breadcrumbLinks' => $breadcrumbLinks,
            ]),
        );
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Message received! We will get back to you soon.');
    }

    public function propertyShow($slug)
    {
        $pageTitle = 'Property Details';
        $breadcrumbLinks = [
            'DescriptionOne' => 'Property details',
            'DescriptionTwo' => 'for',
            'url' => '/property/' . $slug,
            'label' => 'property',
        ];
        $property = App\Models\Property::where('slug', $slug)->firstOrFail();
        return view(
            'pages.property-show',
            with([
                'pageTitle' => $pageTitle,
                'breadcrumbLinks' => $breadcrumbLinks,
                'property' => $property,
            ]),
        );
    }

    public function filterProperties(Request $request)
    {
        $query = App\Models\Property::query();

        if ($request->filled('status')) {
            $query->where('promotion', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('types_id', $request->type);
        }
        if ($request->filled('price')) {
            $query->where('price', '>=', $request->price);
        }
        if ($request->filled('city')) {
            $query->where('town', 'like', '%' . $request->city . '%');
        }

        $properties = $query->paginate(10);
        $pageTitle = 'Filter Results';
        $breadcrumbLinks = [
            'DescriptionOne' => 'Filter results',
            'DescriptionTwo' => 'for',
            'url' => '/properties/filter',
            'label' => 'results',
        ];
        return view(
            'pages.properties',
            with([
                'pageTitle' => $pageTitle,
                'breadcrumbLinks' => $breadcrumbLinks,
                'properties' => $properties,
                'filterParams' => $request->all(),
            ]),
        );
    }
}