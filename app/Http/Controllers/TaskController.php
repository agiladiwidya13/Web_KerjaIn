<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    private function getUser()
    {
        $userId = session('user_id');
        if (! $userId) {
            abort(response()->json(['status' => 'error', 'message' => 'Belum login'], 401));
        }
        $user = User::find($userId);
        if (! $user) {
            abort(response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 401));
        }
        return $user;
    }

    /** POST /api/mitra/programs/{programId}/tasks */
    public function store(Request $request, string $programId)
    {
        $request->validate([
            'judul'    => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
            'deadline' => 'nullable|date',
            'urutan'   => 'nullable|integer|min:1',
        ]);

        $user = $this->getUser();
        $program = Program::where('id', $programId)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        // Auto-urutan jika tidak diisi
        $urutan = $request->urutan ?? ($program->tasks()->max('urutan') + 1);

        $task = Task::create([
            'id'         => (string) Str::uuid(),
            'program_id' => $program->id,
            'judul'      => $request->judul,
            'deskripsi'  => $request->deskripsi,
            'deadline'   => $request->deadline,
            'urutan'     => $urutan,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task berhasil ditambahkan!',
            'data'    => $task,
        ], 201);
    }

    /** POST /api/mitra/tasks/{id}/update */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul'    => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
            'deadline' => 'nullable|date',
            'urutan'   => 'nullable|integer|min:1',
        ]);

        $user = $this->getUser();
        $task = Task::whereHas('program', function ($q) use ($user) {
            $q->where('mitra_id', $user->mitra->id);
        })->findOrFail($id);

        $task->update($request->only(['judul', 'deskripsi', 'deadline', 'urutan']));

        return response()->json(['status' => 'success', 'message' => 'Task berhasil diperbarui!']);
    }

    /** POST /api/mitra/tasks/{id}/delete */
    public function destroy(string $id)
    {
        $user = $this->getUser();
        $task = Task::whereHas('program', function ($q) use ($user) {
            $q->where('mitra_id', $user->mitra->id);
        })->findOrFail($id);

        $task->delete();

        return response()->json(['status' => 'success', 'message' => 'Task berhasil dihapus!']);
    }
}
