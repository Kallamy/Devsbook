<?=$render('header', ['loggedUser'=>$loggedUser]);?>
<section class="container main">
<?=$render('sidebar', ['activeMenu'=>'home']);?>
    <section class="feed mt-10">
    <h1>Configurações</h1>
    <br>
    <form method="POST" action="<?=$base;?>/config">
        <?php if(!empty($flash)): ?>
            <div class="flash"><?php echo $flash ?></div>
        <?php endif; ?>
        <label>
            <h4>Novo Avatar:</h4> <br>
            <input type="file" class="input" name="avatar">
        </label>
        <br>
        <br>
        <label >
            <h4>Nova Capa:</h4> <br>
            <input type="file" class="input" name="cover">
        </label>
        <hr>
        <br>
        <label >
            <h4>Nome Completo:</h4> 
            <input type="text" class="input"  name="name"  value="<?=$loggedUser->name;?>">
        </label>
        <br><br>
        <label >
            <h4>Data de nascimento:</h4> 
            <input type="text" class="input" name="birthdate" value="<?=date('d/m/Y', strtotime( $loggedUser->birthdate));?>" id="birthdate">
        </label>
        <br><br>
        <label >
            <h4>E-mail:</h4> 
            <input type="text"  class="input" name="email" value="<?=$loggedUser->email;?>">
        </label>
        <br><br>
        <label >
            <h4>Cidade:</h4> 
            <input type="text" class="input" name="city" value="<?=$loggedUser->city;?>">
        </label>
        <br><br>
        <label >
            <h4>Trabalho:</h4> 
            <input type="text" class="input" name="work" value="<?=$loggedUser->work;?>">
        </label>
        <hr>
        <br>
        <label >
            <h4>Nova Senha:</h4> 
            <input  type="password" class="input" name="password">
        </label>
        <br><br>
        <label >
            <h4>Confirmar Nova Senha:</h4> 
            <input type="password" class="input" name="password_confirm">
        </label>
        <br><br>
        <input class="button" type="submit" value="Savar" />
    </form>

    </section>
</section>
<script>
IMask( document.getElementById('birthdate'),
        {
        mask: '00/00/0000'
        }
);

    </script>
<?=$render('footer');?>