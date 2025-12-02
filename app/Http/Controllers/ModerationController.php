public function flagProduct($id)
{
    $product = Product::findOrFail($id);
    $product->is_flagged = true;
    $product->save();

    return response()->json(['message' => 'Ürün işaretlendi']);
}

public function suspendUser($id)
{
    $user = User::findOrFail($id);
    $user->is_suspended = true;
    $user->save();

    return response()->json(['message' => 'Kullanıcı askıya alındı']);
}