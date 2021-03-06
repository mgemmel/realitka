<?php

namespace App\Http\Controllers;


use App\Pouzivatel;
use App\Obec;
use App\Inzerat;
use App\Druh;
use App\Realitna_kancelaria;
use App\Typ;
use App\Stav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RealitkaMakleriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $realitka_id = \Auth::user()->realitna_kancelaria_id;
        $makleri = DB::table('pouzivatelia')
            ->join('obce', 'pouzivatelia.obec_id', '=', 'obce.id' )
            ->select('pouzivatelia.id AS id','pouzivatelia.meno AS meno', 'pouzivatelia.priezvisko AS priezvisko', 'pouzivatelia.email AS email', 'pouzivatelia.telefon AS telefon', 'obce.obec AS obec','pouzivatelia.ulica_cislo AS adresa')
            ->where('pouzivatelia.realitna_kancelaria_id', '=', $realitka_id )
            ->where('pouzivatelia.rola', '=', 3 )
            ->paginate(10);






        return view('spravovanie.realitka.makleri.index', ['makleri' => $makleri]);
    }
    public function indexPouzivatel($id)
    {



        $inzeraty = DB::table('inzeraty')
            ->join('pouzivatelia', 'inzeraty.pouzivatel_id', '=', 'pouzivatelia.id' )
            ->join('obce', 'inzeraty.obec_id', '=', 'obce.id' )
            ->join('typy', 'inzeraty.typ_id', '=', 'typy.id' )
            ->select('inzeraty.*', 'pouzivatelia.meno AS meno', 'pouzivatelia.priezvisko AS priezvisko', 'pouzivatelia.email AS email', 'pouzivatelia.telefon AS telefon', 'obce.obec AS obec',
                'obce.okres_id AS okres',
                'typy.nazov AS typ')
            ->where('pouzivatelia.realitna_kancelaria_id', '=', \Auth::user()->realitna_kancelaria_id)
            ->where('pouzivatelia.id', '=', $id )
            ->paginate(10);



        return view('spravovanie.realitka.makleri.indexPouzivatel', ['inzeraty' => $inzeraty]);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $obce = Obec::all();
        return view('spravovanie.realitka.makleri.vytvorit')->with(compact('obce'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [

            'meno' => 'required|string|max:30',
            'priezvisko' => 'required|string|max:30',
            'telefon_pouzivatel' => 'required|string|max:20',
            'email' => 'required|string|email|max:25|unique:pouzivatelia',
            'password' => 'required|string|min:6|confirmed',
            'psc_pouzivatel' => 'required|numeric|min:0|digits:5',
            'obec_pouzivatel' => 'required',
            'ulica_pouzivatel' => 'required|max:20'


        ]);


        $obec_nazov = $request->get('obec_pouzivatel');
        $semicolonPos = strpos($obec_nazov, ',');
        $obec = substr($obec_nazov, 0, $semicolonPos);

        $obec_id = DB::table('obce')
            ->select('id')
            ->where('obec', $obec)->first();



        Pouzivatel::create([
            'obec_id'=>$obec_id->id,
            'realitna_kancelaria_id'=>\Auth::user()->realitna_kancelaria_id,
            'ulica_cislo'=>$request['ulica_pouzivatel'],
            'PSC'=>$request['psc_pouzivatel'],
            'telefon'=>$request['telefon_pouzivatel'],
            'email' => $request['email'],
            'rola'=>3,
            'meno' => $request['meno'],
            'priezvisko' => $request['priezvisko'],
            'password' => bcrypt($request['password'])

        ]);






        return redirect()->action('RealitkaMakleriController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $pouzivatel = Pouzivatel::findOrFail($id);
        $obce = Obec::all();

        return view('spravovanie.realitka.makleri.detail')->with(compact('pouzivatel'))
            ->with(compact('obce'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {



        $pouzivatel = Pouzivatel::findOrFail($id);
        $obce = Obec::all();
        return view('spravovanie.realitka.makleri.upravit')->with(compact('pouzivatel'))
            ->with(compact('obce'));
    }


    public function editProfil($id)
    {



        $pouzivatel = Pouzivatel::findOrFail($id);
        $obce = Obec::all();
        return view('spravovanie.realitka.makleri.upravitProfil')->with(compact('pouzivatel'))
            ->with(compact('obce'));
    }

    public function editFirma($id)
    {



        $pouzivatel = Realitna_kancelaria::findOrFail($id);
        $obce = Obec::all();
        return view('spravovanie.realitka.makleri.upravitFirma')->with(compact('pouzivatel'))
            ->with(compact('obce'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [

            'meno' => 'required|string|max:30',
            'priezvisko' => 'required|string|max:30',
            'telefon_pouzivatel' => 'required|string|max:20',

            'psc_pouzivatel' => 'required|numeric|min:0|digits:5',
            'lokalita' => 'required',
            'ulica_pouzivatel' => 'required|max:20'


        ]);
        $pouzivatel = Pouzivatel::findOrFail($id);
        $obec_nazov = $request->get('lokalita');
        $semicolonPos = strpos($obec_nazov, ',');
        $obec = substr($obec_nazov, 0, $semicolonPos);
        $obecOkres = substr($obec_nazov, $semicolonPos+1, strlen($obec_nazov)+1);
        $obecOkres = str_replace("okres","",$obecOkres);
        $obecOkres = substr($obecOkres, 2, strlen($obec_nazov)+1);


        $obec_id = DB::table('obce')
            ->where('obec', '=',$obec)
            ->where('okres_id','=', $obecOkres)
            ->value('id');

        $pouzivatel->obec_id = $obec_id;
        $pouzivatel->meno=$request->get('meno');
        $pouzivatel->priezvisko=$request->get('priezvisko');

        $pouzivatel->ulica_cislo=$request->get('ulica_pouzivatel');
        $pouzivatel->PSC=$request->get('psc_pouzivatel');
        $pouzivatel->telefon=$request->get('telefon_pouzivatel');
        $pouzivatel->save();








        return redirect()->action('RealitkaMakleriController@show', $pouzivatel->id);
    }
    public function updateProfil(Request $request, $id)
    {
        $this->validate(request(), [

            'meno' => 'required|string|max:30',
            'priezvisko' => 'required|string|max:30',
            'telefon_pouzivatel' => 'required|string|max:20',

            'psc_pouzivatel' => 'required|numeric|min:0|digits:5',
            'lokalita' => 'required',
            'ulica_pouzivatel' => 'required|max:20'


        ]);
        $pouzivatel = Pouzivatel::findOrFail($id);
        $obec_nazov = $request->get('lokalita');
        $semicolonPos = strpos($obec_nazov, ',');
        $obec = substr($obec_nazov, 0, $semicolonPos);
        $obecOkres = substr($obec_nazov, $semicolonPos+1, strlen($obec_nazov)+1);
        $obecOkres = str_replace("okres","",$obecOkres);
        $obecOkres = substr($obecOkres, 2, strlen($obec_nazov)+1);


        $obec_id = DB::table('obce')
            ->where('obec', '=',$obec)
            ->where('okres_id','=', $obecOkres)
            ->value('id');

        $pouzivatel->obec_id = $obec_id;
        $pouzivatel->meno=$request->get('meno');
        $pouzivatel->priezvisko=$request->get('priezvisko');

        $pouzivatel->ulica_cislo=$request->get('ulica_pouzivatel');
        $pouzivatel->PSC=$request->get('psc_pouzivatel');
        $pouzivatel->telefon=$request->get('telefon_pouzivatel');
        $pouzivatel->save();








        return redirect()->action('RealitkaMakleriController@index');
    }

    public function updateFirma(Request $request, $id)
    {
        $this->validate(request(), [

            'nazov' => 'required|string|max:40',
            'lokalita' => 'required',
            'ICO' => 'required|string|min:8|max:8',
            'DIC' => 'required|string|min:10|max:10',
            'kontaktna_osoba' => 'required|string|max:20',

            'ulica_pouzivatel' => 'required|max:20',
            'psc_pouzivatel' => 'required|numeric|min:0|digits:5',
            'telefon_pouzivatel' => 'required|string|max:20'








        ]);

        $pouzivatel = Realitna_kancelaria::findOrFail($id);
        $obec_nazov = $request->get('lokalita');
        $semicolonPos = strpos($obec_nazov, ',');
        $obec = substr($obec_nazov, 0, $semicolonPos);
        $obecOkres = substr($obec_nazov, $semicolonPos+1, strlen($obec_nazov)+1);
        $obecOkres = str_replace("okres","",$obecOkres);
        $obecOkres = substr($obecOkres, 2, strlen($obec_nazov)+1);


        $obec_id = DB::table('obce')
            ->where('obec', '=',$obec)
            ->where('okres_id','=', $obecOkres)
            ->value('id');

        $pouzivatel->obec_id = $obec_id;
        $pouzivatel->nazov=$request->get('nazov');
        $pouzivatel->ICO=$request->get('ICO');
        $pouzivatel->DIC=$request->get('DIC');
        $pouzivatel->kontaktna_osoba=$request->get('kontaktna_osoba');

        $pouzivatel->ulica_cislo=$request->get('ulica_pouzivatel');
        $pouzivatel->PSC=$request->get('psc_pouzivatel');
        $pouzivatel->telefon=$request->get('telefon_pouzivatel');
        $pouzivatel->save();








        return redirect()->action('RealitkaMakleriController@index');
    }




    public function editMakler($id)
    {
        $inzerat = Inzerat::findOrFail($id);

        $obce = Obec::all();
        $druhy = Druh::all();
        $druhy_nazov = Druh::select('nazov')->groupBy('nazov')->get();
        $typy = Typ::all();
        $stavy = Stav::all();

        $makleri = DB::table('pouzivatelia')->
        where('realitna_kancelaria_id', \Auth::user()->realitna_kancelaria_id)->

        get();

        return view('spravovanie.realitka.makleri.upravitMakler')
            ->with(compact('inzerat'))

            ->with(compact('druhy'))
            ->with(compact('druhy_nazov'))

            ->with(compact('stavy'))

            ->with(compact('typy'))

            ->with(compact('obce'))

            ->with(compact('makleri'));

    }







public function updateMakler(Request $request, $id){

    $inzerat = Inzerat::findOrFail($id);
    $idMaklerDelete = $inzerat->pouzivatel_id;
    $inzerat->pouzivatel_id=$request->get('makleri');
    $inzerat->save();

    $inzeraty = DB::table('inzeraty')
        ->where('pouzivatel_id', '=', $idMaklerDelete )
        ->get();
    if($inzeraty->isNotEmpty()){

        $inzeraty = DB::table('inzeraty')
            ->join('pouzivatelia', 'inzeraty.pouzivatel_id', '=', 'pouzivatelia.id' )
            ->join('obce', 'inzeraty.obec_id', '=', 'obce.id' )
            ->join('typy', 'inzeraty.typ_id', '=', 'typy.id' )
            ->select('inzeraty.*', 'pouzivatelia.meno AS meno', 'pouzivatelia.priezvisko AS priezvisko', 'pouzivatelia.email AS email', 'obce.obec AS obec',
                'obce.okres_id AS okres',
                'typy.nazov AS typ')
            ->where('pouzivatelia.realitna_kancelaria_id', '=', \Auth::user()->realitna_kancelaria_id)
            ->where('pouzivatelia.id', '=', $idMaklerDelete )
            ->get();



        return view('spravovanie.realitka.makleri.indexPouzivatelMazanie', ['inzeraty' => $inzeraty]);

    } else {
        Pouzivatel::find($idMaklerDelete)->delete();
    }



    return redirect()->action('RealitkaMakleriController@index');

}

    public function removeMakler($id){
        $inzerat = Inzerat::findOrFail($id);
        $idMaklerDelete = $inzerat->pouzivatel_id;
        Inzerat::find($id)->delete();


        $inzeraty = DB::table('inzeraty')
            ->where('pouzivatel_id', '=', $idMaklerDelete )
            ->get();
        if($inzeraty->isNotEmpty()){

            $inzeraty = DB::table('inzeraty')
                ->join('pouzivatelia', 'inzeraty.pouzivatel_id', '=', 'pouzivatelia.id' )
                ->join('obce', 'inzeraty.obec_id', '=', 'obce.id' )
                ->join('typy', 'inzeraty.typ_id', '=', 'typy.id' )
                ->select('inzeraty.*', 'pouzivatelia.meno AS meno', 'pouzivatelia.priezvisko AS priezvisko', 'pouzivatelia.email AS email', 'obce.obec AS obec',
                    'obce.okres_id AS okres',
                    'typy.nazov AS typ')
                ->where('pouzivatelia.realitna_kancelaria_id', '=', \Auth::user()->realitna_kancelaria_id)
                ->where('pouzivatelia.id', '=', $idMaklerDelete )
                ->get();



            return view('spravovanie.realitka.makleri.indexPouzivatelMazanie', ['inzeraty' => $inzeraty]);

        } else {
            Pouzivatel::find($idMaklerDelete)->delete();
        }



        return redirect()->action('RealitkaMakleriController@index');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inzeraty = DB::table('inzeraty')
            ->where('pouzivatel_id', '=', $id )
            ->get();
        if($inzeraty -> isNotEmpty()){

            $inzeraty = DB::table('inzeraty')
                ->join('pouzivatelia', 'inzeraty.pouzivatel_id', '=', 'pouzivatelia.id' )
                ->join('obce', 'inzeraty.obec_id', '=', 'obce.id' )
                ->join('typy', 'inzeraty.typ_id', '=', 'typy.id' )
                ->select('inzeraty.*', 'pouzivatelia.meno AS meno', 'pouzivatelia.priezvisko AS priezvisko', 'pouzivatelia.email AS email', 'obce.obec AS obec',
                    'obce.okres_id AS okres',
                    'typy.nazov AS typ')
                ->where('pouzivatelia.realitna_kancelaria_id', '=', \Auth::user()->realitna_kancelaria_id)
                ->where('pouzivatelia.id', '=', $id )
                ->get();



            return view('spravovanie.realitka.makleri.indexPouzivatelMazanie', ['inzeraty' => $inzeraty]);

        } else {
            Pouzivatel::find($id)->delete();
        }



        return redirect()->action('RealitkaMakleriController@index');
    }
}
