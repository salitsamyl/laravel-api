<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Resources\ProdukResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();

        // SEARCH
        if ($request->has('search')) {
            $search = $request->search;

            $query->where('namaBarang', 'like', "%{$search}%")
                ->orWhere('kodeBarang', 'like', "%{$search}%");
        }

        // FILTER KATEGORI
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // SORTING HARGA
        if ($request->has('sort')) {

            $sort = $request->sort;

            if ($sort == "harga_asc") {
                $query->orderBy('harga', 'asc');
            }

            if ($sort == "harga_desc") {
                $query->orderBy('harga', 'desc');
            }

        } else {
            $query->latest();
        }

        $produk = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'List Produk',
            'data' => $produk,
            'pagination' => [
                'current_page' => $produk->currentPage(),
                'last_page' => $produk->lastPage(),
                'per_page' => $produk->perPage(),
                'total' => $produk->total(),
                'first_page_url' => $produk->url(1),
                'last_page_url' => $produk->url($produk->lastPage()),
                'next_page_url' => $produk->nextPageUrl(),
                'prev_page_url' => $produk->previousPageUrl(),
                'from' => $produk->firstItem(),
                'to' => $produk->lastItem()
            ]
        ]);
        
            $produk = Produk::paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'List Produk',
                'data' => $produk->items(),
                'pagination' => [
                    'current_page' => $produk->currentPage(),
                    'last_page' => $produk->lastPage(),
                    'per_page' => $produk->perPage(),
                    'total' => $produk->total(),

                    'first_page_url' => $produk->url(1),
                    'last_page_url' => $produk->url($produk->lastPage()),
                    'next_page_url' => $produk->nextPageUrl(),
                    'prev_page_url' => $produk->previousPageUrl(),

                    'from' => $produk->firstItem(),
                    'to' => $produk->lastItem()
                ]
            ]);
    }

    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {

            $file = $request->file('gambar');

            $filename = time().'_'.$file->getClientOriginalName();

            $path = $file->storeAs('produk', $filename, 'public');

            $data['gambar'] = $path;
        }

        $produk = Produk::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dibuat',
            'data' => new ProdukResource($produk)
        ],201);
    }

    public function show($id)
    {
        $produk = Produk::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ProdukResource($produk)
        ]);
    }

    public function update(StoreProdukRequest $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('gambar')) {

            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }

            $path = $request->file('gambar')->store('produk','public');

            $data['gambar'] = $path;
        }

        $produk->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => new ProdukResource($produk)
        ]);
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }
        
        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}
