import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import 'bloc/auth_bloc.dart';

class CompleteProfileScreen extends StatefulWidget {
  const CompleteProfileScreen({super.key});

  @override
  State<CompleteProfileScreen> createState() => _CompleteProfileScreenState();
}

class _CompleteProfileScreenState extends State<CompleteProfileScreen> {
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmController = TextEditingController();
  final _authBloc = AuthBloc();

  String _errorMessage = '';

  void _handleSubmit(String email) {
    setState(() => _errorMessage = '');
    final name = _nameController.text.trim();
    final phone = _phoneController.text.trim();
    final password = _passwordController.text;
    final confirm = _confirmController.text;

    if (name.isEmpty || phone.isEmpty || password.isEmpty || confirm.isEmpty) {
      setState(() => _errorMessage = 'All fields are required.');
      return;
    }
    if (password.length < 8) {
      setState(() => _errorMessage = 'Password must be at least 8 characters.');
      return;
    }
    if (password != confirm) {
      setState(() => _errorMessage = 'Passwords do not match.');
      return;
    }

    _authBloc.add(CompleteProfileEvent(
      email: email,
      name: name,
      phone: phone,
      password: password,
      passwordConfirmation: confirm,
    ));
  }

  @override
  Widget build(BuildContext context) {
    // Read email passed extra or default
    final email = GoRouterState.of(context).extra as String? ?? 'user@example.com';

    return BlocProvider(
      create: (context) => _authBloc,
      child: Scaffold(
        backgroundColor: const Color(0xFFFAFAFA),
        appBar: AppBar(
          title: const Text('COMPLETE YOUR PROFILE'),
        ),
        body: BlocConsumer<AuthBloc, AuthState>(
          listener: (context, state) {
            if (state is AuthSuccess) {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Profile setup completed successfully!')),
              );
              context.go('/home');
            } else if (state is AuthFailure) {
              setState(() => _errorMessage = state.error);
            }
          },
          builder: (context, state) {
            final loading = state is AuthLoading;

            return SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 30),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  
                  const Text(
                    'Almost there! Complete these remaining details to finish setting up your account.',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: Colors.grey,
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 24),

                  if (_errorMessage.isNotEmpty) ...[
                    Text(
                      _errorMessage,
                      style: const TextStyle(
                        color: Colors.red,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                  ],

                  // Full Name
                  const Text(
                    'FULL NAME',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w950,
                      color: Colors.black54,
                    ),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _nameController,
                    enabled: !loading,
                    decoration: InputDecoration(
                      hintText: 'Enter your full name',
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: const BorderSide(color: Color(0xFFFF3F6C)),
                      ),
                    ),
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 18),

                  // Mobile Number
                  const Text(
                    'MOBILE NUMBER',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w950,
                      color: Colors.black54,
                    ),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _phoneController,
                    keyboardType: TextInputType.phone,
                    enabled: !loading,
                    decoration: InputDecoration(
                      hintText: 'Enter your mobile number',
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: const BorderSide(color: Color(0xFFFF3F6C)),
                      ),
                    ),
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 18),

                  // Password
                  const Text(
                    'CREATE PASSWORD',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w950,
                      color: Colors.black54,
                    ),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _passwordController,
                    obscureText: true,
                    enabled: !loading,
                    decoration: InputDecoration(
                      hintText: 'Create a password (min 8 chars)',
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: const BorderSide(color: Color(0xFFFF3F6C)),
                      ),
                    ),
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 18),

                  // Confirm Password
                  const Text(
                    'CONFIRM PASSWORD',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w950,
                      color: Colors.black54,
                    ),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _confirmController,
                    obscureText: true,
                    enabled: !loading,
                    decoration: InputDecoration(
                      hintText: 'Retype your password',
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: const BorderSide(color: Color(0xFFFF3F6C)),
                      ),
                    ),
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 28),

                  ElevatedButton(
                    onPressed: loading ? null : () => _handleSubmit(email),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFF3F6C),
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.grey.shade400,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 16),
                    ),
                    child: Text(
                      loading ? 'SAVING PROFILE...' : 'SAVE & COMPLETE',
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w950, letterSpacing: 1.2),
                    ),
                  ),

                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
