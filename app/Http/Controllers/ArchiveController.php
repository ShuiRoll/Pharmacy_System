<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ArchiveController extends Controller
{
    public function index()
    {
        return view('archives.index', [
            'archivedItems' => $this->trashedRecords(Item::class),
            'archivedSuppliers' => $this->trashedRecords(Supplier::class),
            'archivedLocations' => $this->trashedRecords(Location::class),
            'archivedPurchaseOrders' => $this->trashedRecords(PurchaseOrder::class, ['supplier']),
            'archivedUsers' => $this->trashedRecords(User::class),
        ]);
    }

    public function restore(string $type, int $id)
    {
        $models = [
            'items' => Item::class,
            'suppliers' => Supplier::class,
            'locations' => Location::class,
            'purchase-orders' => PurchaseOrder::class,
            'users' => User::class,
        ];

        abort_unless(isset($models[$type]), 404);

        $record = $models[$type]::withTrashed()->findOrFail($id);
        $record->restore();

        return back()->with('success', ucfirst(str_replace('-', ' ', $type)).' restored successfully.');
    }

    private function trashedRecords(string $modelClass, array $relations = [])
    {
        $model = new $modelClass();

        if (! Schema::hasTable($model->getTable())) {
            return collect();
        }

        $query = $modelClass::onlyTrashed()->latest('deleted_at');

        if ($relations !== []) {
            $query->with($relations);
        }

        return $query->get();
    }
}