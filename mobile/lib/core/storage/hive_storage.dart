import 'package:hive_flutter/hive_flutter.dart';

class HiveStorage {
  static Future<void> init() async {
    await Hive.initFlutter();
    
    // Open boxes for caching cart, user settings, and wishlist products
    await Hive.openBox('settings_box');
    await Hive.openBox('cart_box');
    await Hive.openBox('wishlist_box');
    await Hive.openBox('catalog_box');
    await Hive.openBox('orders_box');
    await Hive.openBox('notifications_box');
    await Hive.openBox('search_history_box');
    await Hive.openBox('profile_box');
  }

  static Box get settings => Hive.box('settings_box');
  static Box get cart => Hive.box('cart_box');
  static Box get wishlist => Hive.box('wishlist_box');
  static Box get catalog => Hive.box('catalog_box');
  static Box get orders => Hive.box('orders_box');
  static Box get notifications => Hive.box('notifications_box');
  static Box get searchHistory => Hive.box('search_history_box');
  static Box get profile => Hive.box('profile_box');
}
