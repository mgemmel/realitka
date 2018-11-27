<?php

namespace App\Http\Controllers;

use App\Fotografia;
use App\Inzerat;
use App\Kategoria;
use App\Kontakt;
use App\Obec;
use App\Realitna_kancelaria;
use App\Typ;
use App\Druh;
use App\Fotografie;
use App\Stav;
use App\Kraj;
use App\Pouzivatel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rules\In;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class InzeratyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        /*predpokladam ze toto je len predpriprava, lebo index metoda by mala primarne zobrazit vsetky inzeraty.
        a ked das vsetko required tak pouzivatelovi nepojde filtrovanie spravne. Zatial som to dal do komentu teda.
        */

        $obce = Obec::all();

        if($request->input('lokalita')){

        $this->validate(request(), [
            'lokalita' => 'required',
            'cena_od' => 'required',
            'cena_do' => 'required',
            'vymera_od' => 'required',
            'vymera_do' => 'required'
        ]);

        $kategoria = $request->input('kategoria');
        $druh = $request->input('druh');
        $stav = $request->input('stav');

        $cena_od = 0;
        $cena_do = 0;
        $vymera_od = 0;
        $vymera_do = 0;

        $kategoria_od = 0;
        $kategoria_do = 0;

        $druh_od = 0;
        $druh_do = 0;

        $stav_od = 0;
        $stav_do = 0;

        if($request->input('cena_od')){
            $cena_od = $request->input('cena_od');
        }else{
            $cena_od = 0;
        }

        if($request->input('cena_do')){
            $cena_do = $request->input('cena_do');
        }else{
            $cena_do = 1000000;
        }

        if($request->input('vymera_od')){
            $vymera_od = $request->input('vymera_od');
        }else{
            $vymera_od = 0;
        }

        if($request->input('vymera_do')){
            $vymera_do = $request->input('vymera_do');
        }else{
            $vymera_do = 1000000;
        }

        if($kategoria == 1){
            $kategoria_od = 1;
            $kategoria_do = 3;
        }else{
            $kategoria_od = $kategoria;
            $kategoria_do = $kategoria;
        }

        if($stav == 1){
            $stav_od = 1;
            $stav_do = 7;
        }else{
            $stav_od = $stav;
            $stav_do = $stav;
        }

        if($druh = 1){
            $druh_od = 1;
            $druh_do = 500;
        }else
            if($druh == 110){
                $druh_od = 101;
                $druh_do = 109;
            }else
                if($druh = 207){
                    $druh_od = 201;
                    $druh_do = 206;
                }else
                    if($druh == 311){
                        $druh_od = 301;
                        $druh_do = 310;
                    }else
                        if($druh == 415){
                            $druh_od = 401;
                            $druh_do = 414;
                        }else{
                            $druh_od = $druh;
                            $druh_do = $druh;
                        }

            $inzeraty = Inzerat::select(DB::raw('inzeraty.*, kategorie.nazov as kategoria, druhy.nazov as druh, druhy.podnazov as podnazov, typy.nazov as typ, stavy.nazov as stav, fotografie.url as url'))
                ->join('kategorie', 'kategoria_id', '=', 'kategorie.id')
                ->join('typy', 'typ_id', '=', 'typy.id')
                ->join('druhy', 'druh_id', '=', 'druhy.id')
                ->join('stavy', 'stav_id', '=', 'stavy.id')
                ->join('fotografie', 'inzerat_id', '=', 'inzeraty.id')
                ->join('obce', 'obec_id', '=', 'obce.id')
                ->where('obce.obec', $request->input('lokalita'))
                ->where('typy.value', $request->input('typ'))
                ->whereBetween('kategorie.value', array($kategoria_od, $kategoria_do))
                ->whereBetween('druhy.value', array($druh_od, $druh_do))
                ->whereBetween('stavy.value', array($stav_od, $stav_do))
                ->whereBetween('cena', array($cena_od, $cena_do))
                ->whereBetween('vymera_domu', array($vymera_od, $vymera_do))
                ->getQuery()
                ->get();
        }else{
            $inzeraty = Inzerat::all();
        }

        return view('inzeraty.filtrovane_inzeraty', ['obce' => $obce, 'inzeraty' => $inzeraty]);

        //zobrazenie inzeratov podla telefonneho cisla
//        if ($request->has('telefon')) {
//            $pouzivatel_id = DB::table('pouzivatelia')->where('telefon', $request->input('telefon'))->value('id');
//            $inzeraty = DB::table('inzeraty')->where('pouzivatel_id', $pouzivatel_id)->get();
//            foreach ($inzeraty as $inzerat) {
//                $inzerat->obrazok = DB::table('fotografie')->where('inzerat_id', $inzerat->id)->value('url');
//            }
//            return view('inzeraty.moje_inzeraty_vysledok', ['inzeraty' => $inzeraty]);
//
//        } else {
//            $inzeraty = Inzerat::with('druh', 'kategoria', 'stav', 'typ', 'kraj')->get();
//
//            //$fotografie = Fotografia::all();
//            foreach ($inzeraty as $inzerat) {
//                $inzerat->obrazok = DB::table('fotografie')->where('inzerat_id', $inzerat->id)->value('url');
//                //$inzerat->obrazok=$obrazok->id;
//            }
//            return view('inzeraty.filtrovane_inzeraty', [ 'obce' => $obce]);
//        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()   // otvorenie viewu pre vytvorenie inzeratu + dynamicke data z db
    {
        $kategorie = Kategoria::all();
        $typy = Typ::all();
        $druhy = Druh::all();
        $druhy_nazov = Druh::select('nazov')->groupBy('nazov')->get();
        $stavy = Stav::all();
        return view('inzeraty.vytvorit_inzerat', ['kategorie' => $kategorie, 'typy' => $typy, 'druhy' => $druhy, 'stavy' => $stavy, 'druhy_nazov' => $druhy_nazov]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)         // metoda pre ulozenie inzeratu + fotiek
    {

        $this->validate(request(), [

            'nazov' => 'required',
            'popis' => 'required',
            'lokalita' => 'required',
            'kraj_id' => 'required',
            'cena' => 'required',
            'druh' => 'required',
            'typ' => 'required',
            'heslo' => 'required',
            'images' => 'required',                   // je potreba mat povinny image ??
            'images.*' => 'image|mimes:jpeg,jpg,png' // zatial validacia iba pre typy v buducnosti mozno aj velkost/mnozstvo

        ]);

        if ($request->hasFile('images')) {  // pre istotu este raz overenie

            // vytvorenie inzeratu
            $inzerat = new Inzerat;

            if (Auth::check()) {
                if (Auth::user()->rola == 1) { //je to admin
                    $inzerat->kategoria_id = 3; //sukromna inzercia
                } else {
                    $inzerat->kategoria_id = 2; //je to bud majitel realitky alebo makler
                }
                $inzerat->pouzivatel_id = Auth::user()->id;
            } else {
                $inzerat->kategoria_id = 3;
                //skontroluje ci zadany email je ten na ktory bol zaslany overovaci kluc
                $pouzivatel = DB::table('pouzivatelia')->where('email', $request->input('email'))->first();
                if ($pouzivatel == null) {
                    return back()->with('error', 'Email nebol overený');
                } else {
                    $inzerat->pouzivatel_id = $pouzivatel->id;
                }
                //porovna kluc z db a ten co bol zadany pri vytvarani ineratu
                if(strcmp($request->input('kluc'),$pouzivatel->email_token)!=0){
                    return back()->with('error', 'Nesprávny overovací kľúč');
                }
            }

            $inzerat->stav_id = $request->get('stavy');
            $inzerat->druh_id = $request->get('druh');
            $inzerat->typ_id = $request->get('typ');
            // $inzerat->kategoria_id = $request->get('kategoria');   //zakomentovane zatial pokym nebude prihlasovanie

            $inzerat->mesto = $request->get('lokalita');
            $inzerat->kraj_id = $request->get('kraj_id');
            $inzerat->ulica = $request->get('ulica');
            $inzerat->nazov = $request->get('nazov');
            $inzerat->popis = $request->get('popis');
            $inzerat->heslo = $request->get('heslo');


            $inzerat->vymera_domu = $request->get('vymera_domu');
            $inzerat->vymera_pozemku = $request->get('vymera_pozemku');
            $inzerat->uzitkova_plocha = $request->get('uzitkova_plocha');

            $cena_dohodou = $request->get('cena_dohodou');              // prichadza z radiobuttonu ako true or false
            if ($cena_dohodou == true) {
                $inzerat->cena_dohodou = 1;
            } else {
                $inzerat->cena_dohodou = 0;
                $inzerat->cena = $request->get('cena');
            }

            $inzerat->updated_at = today();
            $inzerat->save();


            foreach ($request->file('images') as $image) {

                //ulozenie image, do db ide iba url teda path resp.  /public/images/ + image name

                //$file_name = $image->hashName();
                $input['imagename'] = time() . $image->getClientOriginalName();
                $path = public_path('/images');
                $image->move($path, $input['imagename']);

                // vytvorenie fotografie zatial iba jeden obrazok
                $fotografia = new Fotografia;
                $fotografia->inzerat_id = $inzerat->id;
                $fotografia->url = "/images/" . $input['imagename'];
                $fotografia->save();
            }

            return back()->with('success', 'Inzerát bol úspešne pridaný');

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inzerat $inzerat
     * @return \Illuminate\Http\Response
     */
    public function show(Inzerat $inzerat, $id)
    {
        // detail inzeratu si zobrazite na adrese /inzerat/idInzeratu

        $inzerat = Inzerat::findOrFail($id);
        $kategoria = $inzerat->kategoria()->first();
        $druh = $inzerat->druh()->first();
        $stav = $inzerat->stav()->first();
        $typ = $inzerat->typ()->first();
        $kraj = $inzerat->kraj()->first();
        $pouzivatel = $inzerat->pouzivatel()->first();
        $fotografie = DB::table('fotografie')->where('inzerat_id', $id)->get();

        return view('inzeraty.zobrazit_detail')
            ->with(compact('inzerat'))
            ->with(compact('kategoria'))
            ->with(compact('druh'))
            ->with(compact('stav'))
            ->with(compact('typ'))
            ->with(compact('kraj'))
            ->with(compact('fotografie'))
            ->with(compact('pouzivatel'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inzerat $inzerat
     * @return \Illuminate\Http\Response
     */
    public function edit(Inzerat $inzerat, $id)
    {
        $inzerat=Inzerat::find($id);
        $kategorie=Inzerat::find($id);
        $kraje=Inzerat::find($id);
        $druhy=Inzerat::find($id);
        $typ=Inzerat::find($id);
        $stav=Inzerat::find($id);
        $pouzivatelia=Inzerat::find($id);
        return view('inzeraty.upravit_inzeraty')
            ->with(compact('inzerat'))
            ->with(compact('kategorie'))
            ->with(compact('typ'))
            ->with(compact('stav'))
            ->with(compact('kraje'))
            ->with(compact('druhy'))
            ->with(compact('pouzivatelia'));

    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Inzerat $inzerat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inzerat $inzerat, $id)
    {

        $inzerat = Inzerat::find($id);
        $kraje = Inzerat::find($id);
        $pouzivatel = Inzerat::find($id);
        $druhy = Inzerat::find($id);
        $stav = Inzerat::find($id);
        $typ = Inzerat::find($id);
        $kategorie = Inzerat::find($id);

        $inzerat->nazov=request('nazov');
        $inzerat->popis=request('popis');
        $inzerat->mesto=request('mesto');
        $inzerat->ulica=request('ulica');
        $inzerat->cena=request('cena');
        $inzerat->vymera_domu=request('vymera_domu');
        $inzerat->vymera_pozemku=request('vymera_pozemku');
        $inzerat->uzitkova_plocha=request('uzitkova_plocha');
        $inzerat->cena_dohodou=request('cena_dohodou');
        $kraje->nazov=request('nazov');
        $pouzivatel->telefon=request('telefon');
        $druhy->nazov=request('nazov');
        $stav->nazov=request('nazov');
        $typ->nazov=request('nazov');
        $kategorie->nazov=request('nazov');
        $inzerat->save();


        return redirect()->to('inzeraty/'.$id);

    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inzerat $inzerat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inzerat $inzerat, $id)
    {

        Inzerat::find($id)->delete();
        return redirect('/inzeraty')->with('zmazane');
    }

    public function kontakt()
    {
        return view('inzeraty.kontakt');
    }
}
