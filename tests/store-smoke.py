"""Run with python3 tests/store-smoke.py against local XAMPP."""
import http.cookiejar
import re
import subprocess
import urllib.parse
import urllib.request

BASE = 'http://localhost/the-cloth-hub/'
client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
def request(path, data=None):
    body = None if data is None else urllib.parse.urlencode(data).encode()
    return client.open(BASE + path, body).read().decode()
def token(html):
    return re.search(r'name="csrf_token" value="([^"]+)"', html)[1]

product = request('product.php?id=1')
assert 'name="size"' in product, 'Product needs a size selector'
for size in ['S', 'M', 'L', 'XL', 'XXL']:
    assert f'value="{size}"' in product
for size in ['S', 'XL']:
    cart = request('cart.php', dict(action='add', product_id=1, quantity=1, size=size, csrf_token=token(product)))
assert 'Size: S' in cart and 'Size: XL' in cart, 'Sizes must stay separate'
cart = request('cart.php', dict(action='update', **{'quantity[1_S]': 3}, csrf_token=token(cart)))
assert 'value="3"' in cart
cart = request('cart.php', dict(action='remove', cart_key='1_S', csrf_token=token(cart)))
assert 'Size: S<' not in cart and 'Size: XL' in cart
try:
    request('cart.php', dict(action='add', product_id=1, size='BAD', csrf_token=token(cart)))
    raise AssertionError('Invalid size was accepted')
except urllib.error.HTTPError as error:
    assert error.code == 400
try:
    request('cart.php', dict(action='add', product_id=1, size='M'))
    raise AssertionError('Missing CSRF token was accepted')
except urllib.error.HTTPError as error:
    assert error.code == 403
request('login.php', dict(email='customer@clothhub.com', password='password'))
checkout = request('checkout.php')
assert 'Size: XL' in checkout
success = request('checkout.php', dict(address='Smoke test address', csrf_token=token(checkout)))
assert 'Size: XL' in success, 'Saved order must keep size'
order_id = re.search(r'Order Summary \(#(\d+)\)', success)[1]
subprocess.run(['/opt/lampp/bin/mysql', '-u', 'root', '-h', '127.0.0.1', 'clothhub', '-e', f'DELETE FROM orders WHERE id = {int(order_id)} AND shipping_address = "Smoke test address"'], check=True)
print('PASS: size options, separate variants, update/remove, invalid size, CSRF, checkout and saved order')
