import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class OrderActionScreen extends StatefulWidget {
  final String orderId;
  const OrderActionScreen({super.key, required this.orderId});

  @override
  State<OrderActionScreen> createState() => _OrderActionScreenState();
}

class _OrderActionScreenState extends State<OrderActionScreen> {
  String _selectedAction = 'return'; // cancel, return, exchange, refund
  String _reason = '';
  final _textController = TextEditingController();
  bool _loading = false;

  void _submitRequest() {
    final comment = _textController.text.trim();
    if (comment.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please detail the reason for request.')),
      );
      return;
    }

    setState(() {
      _loading = true;
    });

    Future.delayed(const Duration(seconds: 2), () {
      if (mounted) {
        setState(() {
          _loading = false;
        });

        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (context) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              title: const Text('REQUEST FILED!', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 14)),
              content: const Text(
                'Your request has been successfully filed. A support ticket has been created, and our quality control unit will inspect it within 24-48 hours.',
                style: TextStyle(fontSize: 11, height: 1.5, color: Colors.black54),
              ),
              actions: [
                Center(
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.pop(context);
                      context.go('/orders');
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primaryColor,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    child: const Text('DISMISS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                  ),
                )
              ],
            );
          },
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: Text('ORDER SUPPORT $widget.orderId'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/orders'),
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primaryColor)))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Text('REQUEST ACTION TYPE', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
                  const SizedBox(height: 12),

                  Column(
                    children: [
                      RadioListTile<String>(
                        value: 'cancel',
                        groupValue: _selectedAction,
                        title: const Text('Cancel Order', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        subtitle: const Text('Available if packing is not completed.', style: TextStyle(fontSize: 9)),
                        activeColor: AppTheme.primaryColor,
                        onChanged: (val) => setState(() => _selectedAction = val!),
                      ),
                      RadioListTile<String>(
                        value: 'return',
                        groupValue: _selectedAction,
                        title: const Text('Return Product', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        subtitle: const Text('File return logs within 7 days of package delivery.', style: TextStyle(fontSize: 9)),
                        activeColor: AppTheme.primaryColor,
                        onChanged: (val) => setState(() => _selectedAction = val!),
                      ),
                      RadioListTile<String>(
                        value: 'exchange',
                        groupValue: _selectedAction,
                        title: const Text('Exchange Size / Color', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        subtitle: const Text('Exchange with matching variants of similar items.', style: TextStyle(fontSize: 9)),
                        activeColor: AppTheme.primaryColor,
                        onChanged: (val) => setState(() => _selectedAction = val!),
                      ),
                      RadioListTile<String>(
                        value: 'refund',
                        groupValue: _selectedAction,
                        title: const Text('Request Refund', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        subtitle: const Text('File refund directly to original payment wallets.', style: TextStyle(fontSize: 9)),
                        activeColor: AppTheme.primaryColor,
                        onChanged: (val) => setState(() => _selectedAction = val!),
                      ),
                    ],
                  ),
                  const Divider(height: 32),

                  const Text('EXPLAIN DETAILS & REASONS', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _textController,
                    maxLines: 4,
                    decoration: InputDecoration(
                      hintText: 'Enter reasons (e.g. Size was too small, different color received, changed mind...)',
                      hintStyle: const TextStyle(fontSize: 11),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      focusedBorder: const OutlineInputBorder(borderSide: BorderSide(color: AppTheme.primaryColor)),
                    ),
                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 28),

                  ElevatedButton(
                    onPressed: _submitRequest,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primaryColor,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('SUBMIT SUPPORT REQUEST', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                  ),
                ],
              ),
            ),
    );
  }
}
