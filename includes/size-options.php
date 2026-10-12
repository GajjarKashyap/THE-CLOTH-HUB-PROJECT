<div class="form-group">
    <label for="size-<?= (int)$product['id'] ?>">Size</label>
    <select id="size-<?= (int)$product['id'] ?>" name="size" class="form-control" required>
        <option value="">Choose size</option>
        <?php foreach (PRODUCT_SIZES as $size): ?>
            <option value="<?= $size ?>"><?= $size ?></option>
        <?php endforeach; ?>
    </select>
</div>
