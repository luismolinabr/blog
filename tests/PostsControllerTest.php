<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PostsControllerTest extends TestCase
{

    protected $johnName;
    protected $johnEmail;
    protected $johnPassword;
    protected $janeName;
    protected $janeEmail;
    protected $janePassword;
    protected $month;
    protected $monthName;
    protected $year;
    protected $agora;
    protected $mesPassado;
    protected $mesRetrasado;

    public function __construct()
    {
        $this->johnName     = 'John Doe';
        $this->johnEmail    = 'john@example.com';
        $this->johnPassword = '123456';

        $this->janeName     = 'Jane Doe';
        $this->janeEmail    = 'jane@example.com';
        $this->janePassword = '123456';

        date_default_timezone_set('America/Sao_Paulo');

        $this->agora        = Carbon::now();
        $this->mesPassado   = Carbon::now()->subMonthNoOverFlow();
        $this->mesRetrasado = Carbon::now()->subMonthNoOverFlow(2);
    }
    
    /** @test */
    public function visite_a_home_page()
    {
        $this->visit('/')
             ->see('The Bootstrap Blog');
    }

    /** @test */
    public function pode_se_logar_como_Jane()
    {
        $this->createUserJane();

        $this->loginAsJane();

        $this->visit('/')
             ->see($this->janeName)
             ->click('Logout')
             ->seePageIs('/')
             ->see('Login');
    }

    /** @test */
    public function nao_pode_se_registrar_com_dados_incorretos()
    {
        $this->visit('/register')
             ->type($this->johnName, 'name')
             ->type($this->johnEmail, 'email')
             ->type($this->johnPassword, 'password')
             ->type('incorrect_password', 'password_confirmation')
             ->press('Register')
             ->see('A confirmação de password não confere.');
    }

    /** @test */
    public function pode_se_registrar_com_dados_corretos()
    {
        $this->visit('/register')
             ->type($this->johnName, 'name')
             ->type($this->johnEmail, 'email')
             ->type($this->johnPassword, 'password')
             ->type($this->johnPassword, 'password_confirmation')
             ->press('Register')
             ->seePageIs('/');
    }

    /** @test */
    public function nao_pode_se_logar_sem_dados()
    {
        $this->visit('/')
             ->click('Login')
             ->seePageis('/login')
             ->press('Login')
             ->see('Por favor verifique suas credenciais e tente novamente.');
    }

    /** @test */
    public function nao_pode_se_logar_com_dados_incorretos()
    {
        $this->visit('/')
             ->click('Login')
             ->seePageis('/login')
             ->type('incorrec_email@example.com', 'email')
             ->type('incorrect_password', 'password')
             ->press('Login')
             ->see('Por favor verifique suas credenciais e tente novamente.');
    }

    /** @test */
    public function pode_se_logar_com_dados_corretos()
    {
        $this->visit('/')
             ->click('Login')
             ->seePageis('/login')
             ->type($this->johnEmail, 'email')
             ->type($this->johnPassword, 'password')
             ->press('Login')
             ->seePageIs('/')
             ->see('Novo Post');
    }

    /** @test */
    public function nao_pode_criar_um_post_se_nao_estiver_logado()
    {
        $this->visit('/')
             ->dontSee('Novo Post');
    }
 
    /** @test */
    public function nao_pode_criar_um_post_com_dados_incorretos()
    {
        $this->login();

        $this->visit('/')
             ->click('Novo Post')
             ->seePageis('/posts/create')
             ->type('a', 'title')
             ->type('a', 'body')
             ->press('Publish')
             ->see('O campo title deve ter no mínimo 2 caracteres.')
             ->see('O campo body deve ter no mínimo 2 caracteres.');
    }
    
     /** @test */
    public function pode_criar_um_post_com_dados_corretos()
    {
        $this->login();

        $this->visit('/')
             ->click('Novo Post')
             ->seePageis('/posts/create')
             ->type('Meu Primeiro Post', 'title')
             ->type('Este é meu post de teste', 'body')
             ->press('Publish')
             ->seePageIs('/')
             ->see('Meu Primeiro Post');
    }

    /** @test */
    public function nao_pode_ver_o_formulario_para_comentario_se_nao_estiver_logado()
    {
        $this->visit('/')
             ->click('Meu Primeiro Post')
             ->seePageIs('/posts/1')
             ->see('Este é meu post de teste')
             ->dontSee('Add Comment');
    }

    /** @test */
    public function nao_pode_ver_o_formulario_para_comentario_em_seu_proprio_post()
    {
        $this->login();

        $this->visit('/')
             ->click('Meu Primeiro Post')
             ->seePageIs('/posts/1')
             ->see('Este é meu post de teste')
             ->dontSee('Add Comment');
    }

    /** @test */
    public function pode_ver_o_formulario_para_comentario()
    {
        $this->loginAsJane();

        $this->visit('/')
             ->click('Meu Primeiro Post')
             ->seePageIs('/posts/1')
             ->see('Este é meu post de teste')
             ->see('Add Comment');
    }

    /** @test */
    public function nao_pode_adicionar_comentario_com_dados_incorretos()
    {
        $this->loginAsJane();

        $this->visit('/')
             ->click('Meu Primeiro Post')
             ->type('a', 'body')
             ->press('Add Comment')
             ->see('O campo body deve ter no mínimo 2 caracteres.');
    } 

    /** @test */
    public function pode_adicionar_cometario_com_dados_corretos()
    {
        $this->loginAsJane();

        $this->visit('/')
             ->click('Meu Primeiro Post')
             ->type('Ótimo post. Parabéns.', 'body')
             ->press('Add Comment')
             ->see('Ótimo post. Parabéns.')
             ->see("por $this->janeName");
    }

    /** @test */
    public function pode_clicar_e_ver_os_posts_deste_mes()
    {
        $this->createMonthYearVariables($this->agora);

        $this->visit('/')
             ->click("$this->monthName $this->year")
             // ->click("$this->month-$this->year")
             ->seePageIs("/?month=$this->month&year=$this->year")
             ->see('Meu Primeiro Post');
    }

    /** @test */
    public function pode_clicar_e_ver_os_posts_do_mes_passado()
    {
        $this->createPostsMesPassadoEMesRetrasado();

        $this->createMonthYearVariables($this->mesPassado);

        $this->visit('/')
             // ->click("$this->month-$this->year")
             ->click("$this->monthName $this->year")
             ->seePageIs("/?month=$this->month&year=$this->year")
             ->see('Meu post do mês passado')
             ->dontSee('Meu Primeiro Post');
    }

    /** @test */
    public function pode_clicar_e_ver_os_posts_do_mes_retrasado()
    {
        $this->createMonthYearVariables($this->mesRetrasado);

        $this->visit('/')
             // ->click("$this->month-$this->year")
             ->click("$this->monthName $this->year")
             ->seePageIs("/?month=$this->month&year=$this->year")
             ->see('Meu post do mês retrasado')
             ->dontSee('Meu post do mês passado')
             ->dontSee('Meu Primeiro Post');
    }

    /** @test */
    public function o_usuario_pode_fazer_logout()
    {
        $this->login();

        $this->visit('/')
             ->click('Logout')
             ->seePageIs('/')
             ->see('Login');   
    }
    
    protected function login($email = 'john@example.com', $password = '123456')
    {
        $this->visit('/')
             ->click('Login')
             ->seePageis('/login')
             ->type($email, 'email')
             ->type($password, 'password')
             ->press('Login');
    }

    protected function loginAsJane()
    {
        $this->login($this->janeEmail, $this->janePassword);
    }
    
    protected function createUserJane()
    {
        $jane = factory(App\User::class)->create([
            'name'     => $this->janeName,
            'email'    => $this->janeEmail,
            'password' => bcrypt($this->janePassword)
        ]); 
    }

    protected function createMonthYearVariables(Carbon $date)
    {
        $this->month     = $date->format('n');
        $this->monthName = $date->formatLocalized('%B');
        $this->year      = $date->format('Y');
    }
    
    protected function createPostsMesPassadoEMesRetrasado()
    {
        $postMesPassado = factory(App\Post::class)->create([
            'title'      => 'Post Mês Passado',
            'body'       => 'Meu post do mês passado',
            'created_at' => $this->mesPassado
        ]);

        $postMesRetrasado = factory(App\Post::class)->create([
            'title'      => 'Post Mês Retrasado',
            'body'       => 'Meu post do mês retrasado',
            'created_at' => $this->mesRetrasado
        ]);
    }
            
}