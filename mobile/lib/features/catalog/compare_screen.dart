import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/network/api_client.dart';
import '../../core/theme/app_theme.dart';

class CompareScreen extends StatefulWidget {
  const CompareScreen({super.key});

  @override
  State<CompareScreen> createState() => _CompareScreenState();
}

class _CompareScreenState extends State<CompareScreen> {
  final ApiClient _apiClient = ApiClient();
  List<dynamic> _products = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchComparedProducts();
  }

  Future<void> _fetchComparedProducts() async {
    try {
      final res = await _apiClient.post('/api/v1/compare', data: {
        'product_ids': [1, 2],
      });
      if (res.statusCode == 200) {
        setState(() {
          _products = res.data ?? [];
          _isLoading = false;
        });
      } else {
        _loadFallbackCompare();
      }
    } catch (e) {
      _loadFallbackCompare();
    }
  }

  void _loadFallbackCompare() {
    setState(() {
      _products = [
        {
          'id': '1',
          'title': 'Premium Cotton Kurta Set',
          'price': '4,500',
          'brand': 'SD PREMIUM',
          'image': 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=300&auto=format&fit=crop',
          'category': 'Men Ethnic',
          'in_stock': true,
        },
        {
          'id': '2',
          'title': 'Slim Fit Casual Denim Jeans',
          'price': '2,200',
          'brand': 'SD MEN',
          'image': 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=300&auto=format&fit=crop',
          'category': 'Men Western',
          'in_stock': true,
        }
      ];
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('COMPARE PRODUCTS'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/catalog'),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primaryColor)))
          : _products.length < 2
              ? const Center(child: Text('Add at least two items to compare.'))
              : SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: SingleChildScrollView(
                    child: DataTable(
                      headingRowHeight: 180,
                      columns: [
                        const DataColumn(label: SizedBox(width: 80, child: Text('Features', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: Colors.grey)))),
                        ..._products.map((prod) {
                          return DataColumn(
                            label: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Container(
                                  width: 80,
                                  height: 100,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(8),
                                    image: DecorationImage(
                                      image: NetworkImage(prod['image'] ?? ''),
                                      fit: BoxFit.cover,
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 8),
                                SizedBox(
                                  width: 100,
                                  child: Text(
                                    prod['title'] ?? '',
                                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 10),
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                    textAlign: TextAlign.center,
                                  ),
                                ),
                              ],
                            ),
                          );
                        }).toList(),
                      ],
                      rows: [
                        DataRow(cells: [
                          const DataCell(Text('Brand', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 10, color: Colors.grey))),
                          ..._products.map((p) => DataCell(Text(p['brand'] ?? '', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold)))),
                        ]),
                        DataRow(cells: [
                          const DataCell(Text('Price', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 10, color: Colors.grey))),
                          ..._products.map((p) => DataCell(Text('Rs. ${p['price']}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: AppTheme.primaryColor)))),
                        ]),
                        DataRow(cells: [
                          const DataCell(Text('Category', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 10, color: Colors.grey))),
                          ..._products.map((p) => DataCell(Text(p['category'] ?? 'Apparel', style: const TextStyle(fontSize: 10)))),
                        ]),
                        DataRow(cells: [
                          const DataCell(Text('Inventory', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 10, color: Colors.grey))),
                          ..._products.map((p) {
                            final inStock = p['in_stock'] ?? true;
                            return DataCell(
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                decoration: BoxDecoration(
                                  color: inStock ? Colors.green.shade50 : Colors.red.shade50,
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text(
                                  inStock ? 'IN STOCK' : 'OUT OF STOCK',
                                  style: TextStyle(color: inStock ? Colors.green.shade700 : Colors.red.shade700, fontSize: 8, fontWeight: FontWeight.bold),
                                ),
                              ),
                            );
                          }),
                        ]),
                      ],
                    ),
                  ),
                ),
    );
  }
}
