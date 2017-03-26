@foreach($products as $product)
    <option value="{{ $product->product_id }}">{{ $product->product_id }}-{{ $product->ename }}</option>
@endforeach