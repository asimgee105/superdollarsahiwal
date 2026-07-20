import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class InvoiceScreen extends StatelessWidget {
  final String orderId;
  const InvoiceScreen({super.key, required this.orderId});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: Text('INVOICE $orderId'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/orders'),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.download_outlined),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Downloading invoice PDF to directory...')),
              );
            },
          )
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20.0),
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            border: Border.all(color: Colors.grey.shade200),
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.01),
                blurRadius: 10,
                offset: const Offset(0, 4),
              )
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Company details
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('SUPER DOLLAR', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.primaryColor)),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      const Text('TAX INVOICE', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 11, color: Colors.grey)),
                      const SizedBox(height: 4),
                      Text('No: INV-$orderId', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                    ],
                  ),
                ],
              ),
              const Divider(height: 32),

              // Billed to details
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('BILLED TO:', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.grey)),
                      SizedBox(height: 4),
                      Text('Asim Gee', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                      Text('Johar Town, Lahore', style: TextStyle(fontSize: 10, color: Colors.black54)),
                      Text('+923001234567', style: TextStyle(fontSize: 10, color: Colors.black54)),
                    ],
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      const Text('DATE OF ISSUE:', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.grey)),
                      const SizedBox(height: 4),
                      const Text('July 19, 2026', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                      const SizedBox(height: 12),
                      const Text('PAYMENT STATUS:', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.grey)),
                      const SizedBox(height: 4),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.green.shade50,
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text('PAID', style: TextStyle(color: Colors.green.shade800, fontSize: 8, fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                ],
              ),
              const Divider(height: 32),

              // Items table header
              const Row(
                children: [
                  Expanded(flex: 6, child: Text('ITEM DESCRIPTION', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.grey))),
                  Expanded(flex: 2, child: Text('QTY', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.grey, textAlign: Alignment.centerRight))),
                  Expanded(flex: 2, child: Text('TOTAL', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.grey, textAlign: Alignment.centerRight))),
                ],
              ),
              const Divider(height: 16),

              // Items rows
              const Row(
                children: [
                  Expanded(
                    flex: 6,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Premium Cotton Kurta Set', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                        Text('Brand: SD PREMIUM | Size: M', style: TextStyle(color: Colors.grey, fontSize: 9)),
                      ],
                    ),
                  ),
                  Expanded(flex: 2, child: Text('1', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold), textAlign: TextAlign.right)),
                  Expanded(flex: 2, child: Text('Rs. 4,500', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900), textAlign: TextAlign.right)),
                ],
              ),
              const Divider(height: 24),

              // Total block
              Align(
                alignment: Alignment.centerRight,
                child: SizedBox(
                  width: 180,
                  child: Column(
                    children: [
                      const Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Subtotal', style: TextStyle(fontSize: 10, color: Colors.grey)),
                          Text('Rs. 4,500', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      const SizedBox(height: 6),
                      const Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Shipping Fee', style: TextStyle(fontSize: 10, color: Colors.grey)),
                          Text('Rs. 150', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      const Divider(height: 16),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Grand Total', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900)),
                          Text('Rs. 4,650', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: AppTheme.primaryColor)),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
