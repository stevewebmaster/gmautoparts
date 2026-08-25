<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected Cart $cart)
    {
    }

    public function index(): View
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'unavailable' => $this->cart->unavailable(),
            'subtotal' => $this->cart->subtotal(),
            'band' => $this->cart->dominantBand(),
        ]);
    }

    public function add(Part $part): RedirectResponse
    {
        if (! $part->isPurchasable()) {
            return back()->withErrors([
                'cart' => 'Sorry, that part is not available to buy online. Please reserve it or get in touch.',
            ]);
        }

        $this->cart->add($part);

        return redirect()->route('cart.index')->with('success', "\"{$part->title}\" added to your cart.");
    }

    public function remove(int $part): RedirectResponse
    {
        $this->cart->remove($part);

        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()->route('cart.index');
    }
}
