<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Users extends BaseController
{
    protected $helpers = ['form', 'url'];
    protected UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    // LISTAR
    public function index()
    {
        return view('users/index', [
            'users' => $this->users->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    // FORMULARIO DE CREAR
    public function create()
    {
        return view('users/form', ['user' => null]);
    }

    // GUARDAR NUEVO
    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput();
        }

        $this->users->insert($this->request->getPost(['name', 'email', 'password']));

        return redirect()->to('/users')->with('message', 'Usuario creado correctamente.');
    }

    // FORMULARIO DE EDITAR
    public function edit(int $id)
    {
        $user = $this->users->find($id) ?? throw PageNotFoundException::forPageNotFound();

        return view('users/form', ['user' => $user]);
    }

    // ACTUALIZAR
    public function update(int $id)
    {
        $this->users->find($id) ?? throw PageNotFoundException::forPageNotFound();

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
            'password' => 'permit_empty|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput();
        }

        $data = $this->request->getPost(['name', 'email']);

        // Solo cambia la contraseña si escribieron una nueva
        $password = $this->request->getPost('password');
        if (! empty($password)) {
            $data['password'] = $password;
        }

        $this->users->update($id, $data);

        return redirect()->to('/users')->with('message', 'Usuario actualizado correctamente.');
    }

    // ELIMINAR
    public function delete(int $id)
    {
        $this->users->delete($id);

        return redirect()->to('/users')->with('message', 'Usuario eliminado.');
    }
}