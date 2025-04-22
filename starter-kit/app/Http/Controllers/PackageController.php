<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * แสดงรายการแพ็กเกจทั้งหมด
     */
    public function index()
    {
        // คืนค่าเป็น JSON array ของแพ็กเกจทั้งหมด
        return Package::all();
    }

    /**
     * สร้างแพ็กเกจใหม่
     */
    public function store(Request $request)
    {
        // สามารถเพิ่มการตรวจสอบความถูกต้อง (validation) ตามต้องการ
        $request->validate([
            'package_name' => 'required|string|max:255',
            'coins' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $package = Package::create($request->all());

        return response()->json([
            'message' => 'สร้างแพ็กเกจสำเร็จ',
            'data' => $package
        ], 201);
    }

    /**
     * แสดงข้อมูลแพ็กเกจตาม id
     */
    public function show($id)
    {
        $package = Package::findOrFail($id);
        return $package;
    }

    /**
     * อัปเดตข้อมูลแพ็กเกจ
     */
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        // validation เบื้องต้น
        $request->validate([
            'package_name' => 'required|string|max:255',
            'coins' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $package->update($request->all());

        return response()->json([
            'message' => 'อัปเดตแพ็กเกจสำเร็จ',
            'data' => $package
        ], 200);
    }

    /**
     * ลบแพ็กเกจ
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return response()->json([
            'message' => 'ลบแพ็กเกจสำเร็จ'
        ], 204);
    }
}
