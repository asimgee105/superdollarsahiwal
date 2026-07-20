import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class OtpVerificationScreen extends StatefulWidget {
  const OtpVerificationScreen({super.key});

  @override
  State<OtpVerificationScreen> createState() => _OtpVerificationScreenState();
}

class _OtpVerificationScreenState extends State<OtpVerificationScreen> {
  final _otpController = TextEditingController();
  bool _loading = false;
  String _errorMessage = '';

  void _handleVerify(String email) {
    final code = _otpController.text.trim();
    if (code.length < 6) {
      setState(() => _errorMessage = 'Please enter a valid 6-digit code.');
      return;
    }

    setState(() {
      _loading = true;
      _errorMessage = '';
    });

    // Simulate OTP verification and redirect to create password
    Future.delayed(const Duration(seconds: 1), () {
      if (mounted) {
        setState(() => _loading = false);
        if (code == '123456') {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('OTP code verified successfully!')),
          );
          // Redirect to create password screen
          context.go('/create-password', extra: email);
        } else {
          setState(() => _errorMessage = 'Invalid code. Enter 123456 for staging bypass.');
        }
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    // Read email passed as extra
    final email = GoRouterState.of(context).extra as String? ?? 'user@example.com';

    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('VERIFY ACCOUNT'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/forgot-password'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 30),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              'A verification code has been dispatched to your address: $email. Please enter the 6-digit code below to unlock account credentials management.',
              style: const TextStyle(fontSize: 11, color: Colors.grey, height: 1.5, fontWeight: FontWeight.w600),
            ),
            const SizedBox(height: 28),

            if (_errorMessage.isNotEmpty) ...[
              Text(
                _errorMessage,
                style: const TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 12),
            ],

            const Text(
              '6-DIGIT VERIFICATION CODE',
              style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black54),
            ),
            const SizedBox(height: 6),
            TextField(
              controller: _otpController,
              keyboardType: TextInputType.number,
              maxLength: 6,
              enabled: !_loading,
              decoration: InputDecoration(
                hintText: 'Enter code',
                counterText: '',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                focusedBorder: const OutlineInputBorder(borderSide: BorderSide(color: AppTheme.primaryColor)),
              ),
              style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, letterSpacing: 3.0),
            ),
            const SizedBox(height: 28),

            ElevatedButton(
              onPressed: _loading ? null : () => _handleVerify(email),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: Text(
                _loading ? 'VERIFYING CODE...' : 'VERIFY CODE & CONTINUE',
                style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.2),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
