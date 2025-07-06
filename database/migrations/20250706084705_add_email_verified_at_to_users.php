<?php
use Phinx\Migration\AbstractMigration;

class AddEmailVerifiedAtToUsers extends AbstractMigration
{
    public function change()
    {
        $this->table('users')
             ->addColumn('email_verified_at', 'timestamp', ['null' => true, 'default' => null, 'after' => 'password_reset_token'])
             ->update();
    }
}