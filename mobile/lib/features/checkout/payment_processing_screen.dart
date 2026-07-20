import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/storage/hive_storage.dart';
import '../../core/theme/app_theme.dart';

class PaymentProcessingScreen extends StatefulWidget {
  final double amount;
  final String method;
  const PaymentProcessingScreen({super.key, required this.amount, required this.method});

  @override
  State<PaymentProcessingScreen> createState() => _PaymentProcessingScreenState();
}

class _PaymentProcessingScreenState extends State<PaymentProcessingScreen> {
  String _currentStep = 'processing'; // processing, success, pending, failed, retry
  int _countdown = 3;

  @override
  void initState() {
    super.initState();
    _startSimulatedProcessing();
  }

  void _startSimulatedProcessing() {
    Future.delayed(const Duration(seconds: 2), () {
      if (mounted) {
        if (widget.method == 'failed_mock') {
          setState(() {
            _currentStep = 'failed';
          });
        } else if (widget.method == 'pending_mock') {
          setState(() {
            _currentStep = 'pending';
          });
        } else {
          // Success
          HiveStorage.cart.clear(); // Clear cart on success
          setState(() {
            _currentStep = 'success';
          });
        }
      }
    });
  }

  void _handleRetry() {
    setState(() {
      _currentStep = 'processing';
    });
    _startSimulatedProcessing();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Center(
            child: _buildStateContent(),
          ),
        ),
      ),
    );
  }

  Widget _buildStateContent() {
    switch (_currentStep) {
      case 'processing':
        return Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const CircularProgressIndicator(
              valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primaryColor),
              strokeWidth: 4,
            ),
            const SizedBox(height: 24),
            const Text(
              'PROCESSING SECURE PAYMENT',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 1.0, color: Colors.black87),
            ),
            const SizedBox(height: 8),
            Text(
              'Connecting to ${widget.method.toUpperCase()} gateways...',
              style: const TextStyle(fontSize: 10, color: Colors.grey),
            ),
            const SizedBox(height: 16),
            Text(
              'Rs. ${widget.amount.toStringAsFixed(0)}',
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: AppTheme.primaryColor),
            ),
          ],
        );
      case 'success':
        return Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.green.shade50,
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.check_circle, color: Colors.green, size: 64),
            ),
            const SizedBox(height: 24),
            const Text(
              'PAYMENT SUCCESSFUL!',
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w900, letterSpacing: 1.0, color: Colors.green),
            ),
            const SizedBox(height: 8),
            const Text(
              'Your order ODR-849102 has been successfully registered. The invoice has been dispatched to your email.',
              style: TextStyle(fontSize: 11, color: Colors.grey, height: 1.5),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 32),
            ElevatedButton(
              onPressed: () => context.go('/orders'),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('VIEW ORDER STATUS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
            ),
          ],
        );
      case 'pending':
        return Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.amber.shade50,
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.hourglass_empty, color: Colors.amber, size: 64),
            ),
            const SizedBox(height: 24),
            const Text(
              'TRANSACTION PENDING',
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w900, letterSpacing: 1.0, color: Colors.amber),
            ),
            const SizedBox(height: 8),
            const Text(
              'Your transaction is currently undergoing validation check. We will notify you as soon as the status updates.',
              style: TextStyle(fontSize: 11, color: Colors.grey, height: 1.5),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 32),
            ElevatedButton(
              onPressed: () => context.go('/orders'),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.black,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('CHECK ORDER DIALOGS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
            ),
          ],
        );
      case 'failed':
      default:
        return Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.cancel, color: Colors.red, size: 64),
            ),
            const SizedBox(height: 24),
            const Text(
              'PAYMENT FAILED',
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w900, letterSpacing: 1.0, color: Colors.red),
            ),
            const SizedBox(height: 8),
            const Text(
              'The banking server rejected your payment transaction request. Please verify card details or try alternative methods.',
              style: TextStyle(fontSize: 11, color: Colors.grey, height: 1.5),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 32),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                OutlinedButton(
                  onPressed: () => context.go('/checkout'),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: Colors.black, width: 1.5),
                    foregroundColor: Colors.black,
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: const Text('CANCEL', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900)),
                ),
                const SizedBox(width: 12),
                ElevatedButton(
                  onPressed: _handleRetry,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primaryColor,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: const Text('RETRY PAYMENT', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900)),
                ),
              ],
            )
          ],
        );
    }
  }
}
