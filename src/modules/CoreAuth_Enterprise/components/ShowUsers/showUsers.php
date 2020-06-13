<table class="table">
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
    </tr>
    <?php foreach ($users as $user) { ?>

        <tr>
            <td><?= $user->id ?></td>
            <td><?= $user->firstName ?></td>
            <td><?= $user->lastName ?></td>
            <td><?= $user->username ?></td>
        </tr>
    <?php } ?>
</table>