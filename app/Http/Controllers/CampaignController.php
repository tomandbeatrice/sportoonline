public function destroy($id)
{
    $campaign = Campaign::findOrFail($id);
    $campaign->delete();

    return response()->json(['message' => 'Kampanya silindi']);
}

public function update(Request $request, $id)
{
    $campaign = Campaign::findOrFail($id);
    $campaign->update($request->only(['title', 'discount', 'segment_id']));

    return response()->json(['message' => 'Kampanya güncellendi']);
}