<section class="rb-container">
    <div class="sec-head"><h2 class="sec-title">Kelola <span>User</span></h2></div>
    <table class="rb-table">
        <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
        <?php foreach ($users as $userRow): ?>
            <tr>
                <td>
                    <?= e($userRow['name']) ?>
                    <?php if ($userRow['delete_requested']): ?>
                        <span class="rb-status" style="background:#fffbeb;color:#d97706;border-color:#fde68a;font-size:10px;margin-left:6px;padding:2px 6px;">Minta Hapus</span>
                    <?php endif; ?>
                </td>
                <td><?= e($userRow['email']) ?></td>
                <td><?= e($userRow['role']) ?></td>
                <td><span class="rb-status"><?= e($userRow['status']) ?></span></td>
                <td class="rb-actions" style="display:flex;gap:8px;">
                    <?php if ($userRow['delete_requested']): ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin menyetujui penghapusan akun ini?')">
                            <input type="hidden" name="action" value="approve_delete_user">
                            <input type="hidden" name="user_id" value="<?= $userRow['id'] ?>">
                            <button class="btn-primary" style="background:#dc2626;border-color:#dc2626;">Approve Hapus</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="reject_delete_user">
                            <input type="hidden" name="user_id" value="<?= $userRow['id'] ?>">
                            <button class="btn-secondary">Tolak Hapus</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($userRow['role'] === 'seller' && $userRow['status'] === 'pending'): ?>
                        <form method="post" style="display:inline;"><input type="hidden" name="action" value="approve_seller"><input type="hidden" name="seller_id" value="<?= $userRow['id'] ?>"><button class="btn-primary">Approve</button></form>
                    <?php endif; ?>
                    <?php if ($userRow['role'] !== 'admin'): ?>
                        <form method="post" style="display:inline;"><input type="hidden" name="action" value="ban_user"><input type="hidden" name="user_id" value="<?= $userRow['id'] ?>"><button class="btn-secondary rb-danger">Ban</button></form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
