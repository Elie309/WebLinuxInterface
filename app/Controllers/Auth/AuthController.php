<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $session;
    protected $validation;
    protected $email;
    
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->email = \Config\Services::email();
    }
    
    public function index()
    {
        // Check if user is already logged in
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        // Display login page
        return view('auth/login');
    }
    
    public function login()
    {
        // Set validation rules
        $this->validation->setRules([
            'email' => 'required',
            'password' => 'required|min_length[6]'
        ]);
        
        // Validate input
        if (!$this->validation->withRequest($this->request)->run()) {
            return redirect()->to('/login')->withInput()->with('errors', $this->validation->getErrors());
        }
        
        // Get form data
        $username = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        // Here you would implement your actual authentication logic
        // This is a simple example - you should use a model to check credentials
        
        // Example authentication logic (replace with your actual implementation)
        if ($username === 'admin@admin.com' && password_verify($password, password_hash('admin123', PASSWORD_DEFAULT))) {
            // Set session data
            $userData = [
                'id'        => 1,
                'email'  => $username,
                'isLoggedIn' => true
            ];
            
            $this->session->set($userData);
            return redirect()->to('/dashboard');
        }
        
        // If authentication fails
        return redirect()->to('/login')->with('error', 'Invalid username or password');
    }
    
    public function logout()
    {
        // Destroy session
        $this->session->destroy();
        
        return redirect()->to('/login')->with('success', 'You have been logged out successfully');
    }
    
    public function forgotPassword()
    {
        // If GET request, show form
        if ($this->request->getMethod() === 'get') {
            return view('auth/forgot_password');
        }
        
        // If POST request, process the form
        $this->validation->setRules([
            'email' => 'required|valid_email'
        ]);
        
        if (!$this->validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $this->validation->getErrors());
        }
        
        $email = $this->request->getPost('email');
        
        // Here you would check if email exists in your database
        // This is a placeholder. You should implement your own logic
        
        // Generate token for password reset
        $token = bin2hex(random_bytes(32));
        
        // Store token in database with user email and expiry time
        // You would implement this with your model
        
        // Send email with reset link
        $resetLink = site_url("reset-password/{$token}");
        
        $this->email->setTo($email);
        $this->email->setSubject('Password Reset');
        $this->email->setMessage("Please click the link to reset your password: {$resetLink}");
        
        if ($this->email->send()) {
            return redirect()->to('/login')->with('success', 'Password reset link has been sent to your email');
        } else {
            return redirect()->back()->with('error', 'Failed to send email. Please try again.');
        }
    }
    
    public function resetPassword($token = null)
    {
        // If token is not provided
        if ($token === null) {
            return redirect()->to('/login');
        }
        
        // If GET request, show form
        if ($this->request->getMethod() === 'get') {
            // Check if token is valid (you would implement this with your model)
            // This is a placeholder
            $isValid = true; // Replace with actual validation
            
            if (!$isValid) {
                return redirect()->to('/login')->with('error', 'Invalid or expired token');
            }
            
            return view('auth/reset_password', ['token' => $token]);
        }
        
        // If POST request, process the form
        $this->validation->setRules([
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ]);
        
        if (!$this->validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $this->validation->getErrors());
        }
        
        $password = $this->request->getPost('password');
        
        // Update user password in database
        // You would implement this with your model
        // This is a placeholder
        $success = true; // Replace with actual update process
        
        if ($success) {
            return redirect()->to('/login')->with('success', 'Password has been reset successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to reset password. Please try again.');
        }
    }
}
