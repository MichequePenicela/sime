<?php
use App\Core\Router;


// Rotas para Login
Router::get('/login', 'AuthController@showLoginForm'); // exibe formulário
Router::post('/login', 'AuthController@login');       // envia dados do login
Router::get('/logout', 'AuthController@logout');
// Rota para Home
Router::get('/', 'HomeController@index');
Router::get('/notfound', 'HomeController@erro404');
// ------------------------ Rotas de Membros ------------------------
Router::get('/membros', 'MembroController@index');
Router::get('/membros/search', 'MembroController@search');  // retorna JSON de membros
//Router::post('/membros/save', 'MembroController@save');    // salva via AJAX
Router::post('/membros/verificarSimilaridade', 'MembroController@verificarSimilaridade');
Router::post('/membros/confirmarCadastro', 'MembroController@confirmarCadastro');
Router::get('/membros/comparacao', 'MembroController@comparacao');
Router::post('/membros/update', 'MembroController@update'); //Atualizar membro
Router::get('/membros/dashboard', 'MembroController@dashboard'); // ?id=...
Router::get('/membros/datamembers', 'MembroController@datamembers');
Router::get('/membros/datamember', 'MembroController@datamember');
Router::post('/membros/addParentesco', 'MembroController@addParentesco');
Router::get('/membros/listarTiposParentesco', 'MembroController@listarTiposParentesco');
Router::post('/membros/delete', 'MembroController@delete');
Router::post('/membros/deleteParente', 'MembroController@deleteParente');
//---------------------------Rotas de Participacoes---------------------
Router::get('/membros/dadosCulto', 'ParticipacoesController@dadosCulto');
Router::post('/membros/add', 'ParticipacoesController@add');
Router::post('/membros/editarDadosCulto', 'ParticipacoesController@editarDadosCulto');
Router::post('/membros/excluirDadosCulto', 'ParticipacoesController@excluirDadosCulto');

//-------------------------Rotas de Dizimos----------------------------
Router::post('/membros/addDizimo', 'MembroController@addDizimo');
Router::get('/membros/painelDizimos', 'DizimoController@painelDizimos');
Router::get('/membros/buscarDizimista', 'DizimoController@buscarDizimista');
Router::get('/membros/painelIndividual', 'DizimoController@painelIndividual');
Router::get('/membros/dizimosPorMembro', 'DizimoController@dizimosPorMembro');
Router::post('/membros/atualizarDizimo', 'DizimoController@atualizarDizimo');
Router::post('/membros/deleteDizimo/{id}', 'DizimoController@deleteDizimo');
//-------------------------Rotas de Contribuicoes----------------------------
Router::get('/contribuicao', 'ContribuicaoController@index');
Router::post('/contribuicao/addContribuicao', 'ContribuicaoController@addContribuicao');
Router::get('/contribuicao/buscarContribuinte', 'ContribuicaoController@buscarContribuinte');
Router::get('/contribuicao/dashboard', 'ContribuicaoController@painel');
Router::get('/contribuicao/contribuicaoPorMembro', 'ContribuicaoController@contribuicaoPorMembro');
Router::post('/contribuicao/atualizarcontribuicao', 'ContribuicaoController@atualizarcontribuicao');
Router::post('/contribuicao/deletecontribuicao/{id}', 'ContribuicaoController@deletecontribuicao');

//-------------------------Rotas financeiras---------------------------
Router::get('/finance', 'FinanceiroController@index');
Router::post('/finance/addEntrada', 'FinanceiroController@addEntrada');
Router::post('/finance/addDespesa', 'FinanceiroController@addDespesa');
Router::get('/finance/pesquisa', 'FinanceiroController@pesquisa');
Router::post('/finance/buscar', 'FinanceiroController@buscar');
Router::get('/finance/dashboard', 'FinanceiroController@dashboard');
Router::post('/finance/excluir', 'FinanceiroController@excluir');
Router::post('/finance/editar', 'FinanceiroController@editar');
//-------------------------Rotas de Usuarios---------------------------
Router::get('/usuarios', 'UsuariosController@dashboard');
Router::get('/usuarios/search', 'UsuariosController@search');
Router::get('/usuarios/painel', 'UsuariosController@painel');
Router::post('/usuarios/add', 'UsuariosController@add');
Router::post('/usuarios/update', 'UsuariosController@update');
Router::post('/usuarios/delete', 'UsuariosController@delete');
Router::post('/usuarios/toggleStatus', 'UsuariosController@toggleStatus');
//-------------------------Rotas de perfil---------------------------
Router::get('/profile', 'ProfileController@index');
//-------------------------Rotas relatorios---------------------------
Router::get('/relatorios', 'RelatoriosController@index');
Router::get('/relatorios/scan', 'RelatoriosController@scan');
Router::post('/relatorios/addReport', 'RelatoriosController@addReport');
Router::get('/relatorios/runReport', 'RelatoriosController@runReport');
Router::post('/relatorios/run', 'RelatoriosController@run');
Router::get('/relatorios/download/{id}', 'RelatoriosController@download');
Router::get('/relatorios/deleteReportLog/{id}', 'RelatoriosController@deleteReportLog');
//-------------------------Rotas Administracao do Sistema---------------------------
Router::get('/administracao', 'AdministracaoController@index');
//-------------------------Rotas de Notificacoes---------------------------
Router::get('/notificacoes', 'NotificacoesController@index');
Router::get('/notificacoes/ajax', 'NotificacoesController@ajax');
//-------------------------Rotas de Politicas do SIME---------------------------
Router::get('/politica-privacidade', 'PoliticasController@politica');
Router::get('/politica-dados', 'PoliticasController@dados');
Router::get('/politica-organizacao', 'PoliticasController@organizacao');