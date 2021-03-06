<?php

namespace App\Http\Controllers;

use App\Fotografia;
use App\Inzerat;
use App\Kategoria;
use App\Kontakt;
use App\Mail\ZabudnuteHesloMail;
use App\Obec;
use App\Pouzivatel;
use foo\bar;
use Illuminate\Support\Facades\Mail;
use App\Typ;
use App\Druh;
use App\Stav;
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

        if ($request->input('lokalita')) {
            $this->validate(request(), [
//                'lokalita' => 'required',
                /*'cena_od' => 'required',
                'cena_do' => 'required',
                'vymera_od' => 'required',
                'vymera_do' => 'required'*/
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

            if ($request->input('cena_od')) {
                $cena_od = $request->input('cena_od');
            } else {
                $cena_od = 0;
            }

            if ($request->input('cena_do')) {
                $cena_do = $request->input('cena_do');
            } else {
                $cena_do = 1000000;
            }

            if ($request->input('vymera_od')) {
                $vymera_od = $request->input('vymera_od');
            } else {
                $vymera_od = 0;
            }

            if ($request->input('vymera_do')) {
                $vymera_do = $request->input('vymera_do');
            } else {
                $vymera_do = 1000000;
            }

            if ($kategoria == 1) {
                $kategoria_od = 1;
                $kategoria_do = 3;
            } else {
                $kategoria_od = $kategoria;
                $kategoria_do = $kategoria;
            }

            if ($stav == 1) {
                $stav_od = 1;
                $stav_do = 7;
            } else {
                $stav_od = $stav;
                $stav_do = $stav;
            }

            if ($druh = 1) {
                $druh_od = 1;
                $druh_do = 500;
            } else
                if ($druh == 110) {
                    $druh_od = 101;
                    $druh_do = 109;
                } else
                    if ($druh = 207) {
                        $druh_od = 201;
                        $druh_do = 206;
                    } else
                        if ($druh == 311) {
                            $druh_od = 301;
                            $druh_do = 310;
                        } else
                            if ($druh == 415) {
                                $druh_od = 401;
                                $druh_do = 414;
                            } else {
                                $druh_od = $druh;
                                $druh_do = $druh;
                            }

//            dd($request->get('cena_od'),
////                $request->get('cena_do'),
////                $request->get('vymera_od'),
////                $request->get('vymera_do'),
////                $request->get('typ'),
////                $druh_od, $druh_do,
////                $stav_od, $stav_do,
////                $kategoria_od, $kategoria_do,
////                $request->get('obec_id'),
////                $request->get('lokalita'));
            //var_dump($request->input('cena_od').'-'.$request->input('cena_do'));die;
            if ($request->filled('vymera_od') || $request->filled('vymera_do')) {
                $inzeraty = Inzerat::select(DB::raw('inzeraty.*'))
                    ->join('kategorie', 'kategoria_id', '=', 'kategorie.id')
                    ->join('typy', 'typ_id', '=', 'typy.id')
                    ->join('druhy', 'druh_id', '=', 'druhy.id')
                    ->join('stavy', 'stav_id', '=', 'stavy.id')
//                ->join('fotografie', 'inzerat_id', '=', 'inzeraty.id')
//                ->join('obce', 'obec_id', '=', 'obce.id')
                    ->where('obec_id', $request->input('obec_id'))
                    ->where('typy.value', $request->input('typ'))
                    ->whereBetween('kategorie.value', array($kategoria_od, $kategoria_do))
                    ->whereBetween('druhy.value', array($druh_od, $druh_do))
                    ->whereBetween('stavy.value', array($stav_od, $stav_do))
                    ->whereBetween('cena', array($cena_od, $cena_do))
                    ->whereBetween('vymera_domu', array($vymera_od, $vymera_do))
                    //->getQuery()
                    ->paginate(10);
                //echo 'ano';
            } else {
                $inzeraty = Inzerat::select(DB::raw('inzeraty.*'))
                    ->join('kategorie', 'kategoria_id', '=', 'kategorie.id')
                    ->join('typy', 'typ_id', '=', 'typy.id')
                    ->join('druhy', 'druh_id', '=', 'druhy.id')
                    ->join('stavy', 'stav_id', '=', 'stavy.id')
//                ->join('fotografie', 'inzerat_id', '=', 'inzeraty.id')
//                ->join('obce', 'obec_id', '=', 'obce.id')
                    ->where('obec_id', $request->input('obec_id'))
                    ->where('typy.value', $request->input('typ'))
                    ->whereBetween('kategorie.value', array($kategoria_od, $kategoria_do))
                    ->whereBetween('druhy.value', array($druh_od, $druh_do))
                    ->whereBetween('stavy.value', array($stav_od, $stav_do))
                    ->whereBetween('cena', array($cena_od, $cena_do))
                    //->getQuery()
                    ->paginate(10);
                //echo 'nie';
            }
            //var_dump($inzeraty);die;

        } else if ($request->input('email')) {
            $pouzivatel_id = DB::table('pouzivatelia')->where('email', $request->input('email'))->value('id');
            if ($pouzivatel_id != null) {
                $inzeraty = Inzerat::select(DB::raw('inzeraty.*'))->where('pouzivatel_id', $pouzivatel_id)->paginate(10);
            } else {
                $inzeraty = null;
            }

        } else {
            $inzeraty = Inzerat::paginate(10);
        }
        if ($inzeraty->count()) {
            foreach ($inzeraty as $inzerat) {
                $inzerat->cena = number_format($inzerat->cena, 2, ",", " ");
                if ($inzerat->jednaFotografia()->value('url') == null) {
                    $inzerat->obrazok = 'images/demo/no_image.jpg';
                } else {
                    $inzerat->obrazok = $inzerat->jednaFotografia()->value('url');
                }
            }
        }
        return view('inzeraty.filtrovane_inzeraty', ['obce' => $obce, 'inzeraty' => $inzeraty, 'widget' => $this->widget()]);

    }


    /*
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()   // otvorenie viewu pre vytvorenie inzeratu + dynamicke data z db
    {
        $typy = Typ::all();
        $druhy = Druh::all();
        $druhy_nazov = Druh::select('nazov')->groupBy('nazov')->get();
        $stavy = Stav::all();
        $obce = Obec::all();
        return view('inzeraty.vytvorit_inzerat', ['typy' => $typy, 'druhy' => $druhy, 'stavy' => $stavy, 'druhy_nazov' => $druhy_nazov, 'obce' => $obce]);
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

            'nazov' => 'required|max:25',
            'popis' => 'required|max:200',
            'ulica' => 'required|max:20',
            'lokalita' => 'required',
            'druh' => 'required',
            'typ' => 'required',
            'images' => 'required|max:10240',                   // je potreba mat povinny image ??
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
                if (strcmp($request->input('kluc'), $pouzivatel->email_token) != 0) {
                    return back()->with('error', 'Nesprávny overovací kľúč');
                }
            }

            $inzerat->stav_id = $request->get('stavy');
            $inzerat->druh_id = $request->get('druh');
            $inzerat->typ_id = $request->get('typ');
            // $inzerat->kategoria_id = $request->get('kategoria');   //zakomentovane zatial pokym nebude prihlasovanie


            $obec_nazov = $request->get('lokalita');
            $semicolonPos = strpos($obec_nazov, ',');
            $obec = substr($obec_nazov, 0, $semicolonPos);

            $obec_id = DB::table('obce')
                ->select('id')
                ->where('obec', $obec)->first();
            $inzerat->obec_id = $obec_id->id;


            $inzerat->ulica = $request->get('ulica');
            $inzerat->nazov = $request->get('nazov');
            $inzerat->popis = $request->get('popis');
            $inzerat->heslo = $request->get('heslo');


            $inzerat->vymera_domu = $request->get('vymera_domu');
            $inzerat->vymera_pozemku = $request->get('vymera_pozemku');
            $inzerat->uzitkova_plocha = $request->get('uzitkova_plocha');

            $cena_dohodou = $request->get('cena_dohodou');              // prichadza z radiobuttonu ako true or false
            if ($cena_dohodou == "true" & $request->get('cena') == "") {
                $inzerat->cena_dohodou = 1;
            } else if ($cena_dohodou == "false" & $request->get('cena') != "") {
                $inzerat->cena_dohodou = 0;
                $inzerat->cena = $request->get('cena');
            } else {
                return back()->with('error', 'Prosíme Vás zadajte cenu alebo nastavte položku CENA DOHODOU na áno');
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
    public function show($id)
    {
        // detail inzeratu si zobrazite na adrese /inzerat/idInzeratu

        $inzerat = Inzerat::findOrFail($id);
        $inzerat->cena = number_format($inzerat->cena, 2, ",", " ");
        $kategoria = $inzerat->kategoria()->first();
        $druh = $inzerat->druh()->first();
        $stav = $inzerat->stav()->first();
        $typ = $inzerat->typ()->first();
        $pouzivatel = $inzerat->pouzivatel()->first();
        $obec = $inzerat->obec()->first();
        $fotografie = DB::table('fotografie')->where('inzerat_id', $id)->get();


        return view('inzeraty.zobrazit_detail')
            ->with(compact('inzerat'))
            ->with(compact('kategoria'))
            ->with(compact('druh'))
            ->with(compact('stav'))
            ->with(compact('cena'))
            ->with(compact('typ'))
            ->with(compact('obec'))
            ->with(compact('fotografie'))
            ->with(compact('pouzivatel'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inzerat $inzerat
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $inzerat = Inzerat::findOrFail($id);

        if (Auth::check()) {                    // si lognuty


            $mobil = Auth::user()->telefon;

            $obce = Obec::all();
            $druhy = Druh::all();
            $druhy_nazov = Druh::select('nazov')->groupBy('nazov')->get();
            $typy = Typ::all();
            $stavy = Stav::all();


            return view('inzeraty.upravit_inzeraty')
                ->with(compact('inzerat'))
                ->with(compact('mobil'))
                ->with(compact('druhy'))
                ->with(compact('druhy_nazov'))
                ->with(compact('stavy'))
                ->with(compact('typy'))
                ->with(compact('obce'));


        } else {                                        // nie si , daj heslo

            if ($request->has('heslo')) {
                if ($inzerat->heslo == $request->get('heslo')) {                        // heslo je ok


                    $obce = Obec::all();
                    $druhy = Druh::all();
                    $druhy_nazov = Druh::select('nazov')->groupBy('nazov')->get();
                    $typy = Typ::all();
                    $stavy = Stav::all();


                    return view('inzeraty.upravit_inzeraty')
                        ->with(compact('inzerat'))
                        ->with(compact('druhy'))
                        ->with(compact('druhy_nazov'))
                        ->with(compact('stavy'))
                        ->with(compact('typy'))
                        ->with(compact('obce'));


                } else {
                    if($inzerat->crawler!=1) {
                        $pouzivatel = Pouzivatel::where('id', $inzerat->pouzivatel_id)->first();
                        Mail::to($pouzivatel->email)->send(new ZabudnuteHesloMail($inzerat));
                        return back()->with('error', 'Zadali ste nesprávne heslo. Heslo bolo odoslané na Vašu emailovú adresu');
                    }else{
                        return back();
                    }
                }

                // neni ok

            } else                                                                          // nezadal si heslo
                return view('inzeraty.zadaj_heslo', ['inzerat' => $inzerat]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Inzerat $inzerat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::check()) {
            $this->validate(request(), [

                'nazov' => 'required|max:25',
                'popis' => 'required|max:200',
                'ulica' => 'required|max:20',
                'telefon_pouzivatel' => 'required|max:20',
                'lokalita' => 'required',
                'druh' => 'required',
                'typ' => 'required',
                'images' => 'max:10240',                   // je potreba mat povinny image ??
                'images.*' => 'image|mimes:jpeg,jpg,png' // zatial validacia iba pre typy v buducnosti mozno aj velkost/mnozstvo

            ]);


        }else {

            $this->validate(request(), [

                'nazov' => 'required|max:25',
                'popis' => 'required|max:200',
                'ulica' => 'required|max:20',
                'lokalita' => 'required',
                'druh' => 'required',
                'typ' => 'required',
                'images' => 'max:10240',                   // je potreba mat povinny image ??
                'images.*' => 'image|mimes:jpeg,jpg,png' // zatial validacia iba pre typy v buducnosti mozno aj velkost/mnozstvo

            ]);
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                //ulozenie image, do db ide iba url teda path resp.  /public/images/ + image name

                //$file_name = $image->hashName();
//            if(Fotografia::where('inzerat_id', $id)->count()) {
                $input['imagename'] = time() . $image->getClientOriginalName();
                $path = public_path('/images');
                $image->move($path, $input['imagename']);

                // vytvorenie fotografie zatial iba jeden obrazok
                $fotografia = new Fotografia;
                $fotografia->inzerat_id = $id;
                $fotografia->url = "/images/" . $input['imagename'];
                $fotografia->save();
//            }
            }
        }

        $comingIDs = json_decode($request->get('ids'));

        $rows = DB::table('fotografie')->whereIn('id', $comingIDs);
        $rows->delete();


        $inzerat = Inzerat::findOrFail($id);

        $inzerat->nazov = $request->get('nazov');
        $cena_dohodou = $request->get('cena_dohodou');              // prichadza z radiobuttonu ako true or false
        if ($cena_dohodou == "true" & $request->get('cena') == "") {
            $inzerat->cena_dohodou = 1;
            $inzerat->cena = null;
        } else if ($cena_dohodou == "false" & $request->get('cena') != "") {
            $inzerat->cena_dohodou = 0;
            $inzerat->cena = $request->get('cena');
        } else {
            return back()->with('error', 'Prosíme Vás zadajte cenu alebo nastavte položku CENA DOHODOU na áno');
        }


        $obec_nazov = $request->get('lokalita');
        $semicolonPos = strpos($obec_nazov, ',');
        $obec = substr($obec_nazov, 0, $semicolonPos);
        $obecOkres = substr($obec_nazov, $semicolonPos + 1, strlen($obec_nazov) + 1);
        $obecOkres = str_replace("okres", "", $obecOkres);
        $obecOkres = substr($obecOkres, 2, strlen($obec_nazov) + 1);


        $obec_id = DB::table('obce')
            ->where('obec', '=', $obec)
            ->where('okres_id', '=', $obecOkres)
            ->value('id');


        $inzerat->obec_id = $obec_id;


        $inzerat->ulica = $request->get('ulica');
        $inzerat->druh_id = $request->get('druh');
        $inzerat->typ_id = $request->get('typ');
        $inzerat->popis = $request->get('popis');
        $inzerat->vymera_domu = $request->get('vymera_domu');
        $inzerat->vymera_pozemku = $request->get('vymera_pozemku');
        $inzerat->uzitkova_plocha = $request->get('uzitkova_plocha');


        $inzerat->stav_id = $request->get('stavy');


        $inzerat->nazov = $request->get('nazov');

        if ($request->get('telefon_pouzivatel') != null) {

            if (Auth::check()) {                    // si lognuty


                $id = \Auth::user()->id;
                $pouzivatel = Pouzivatel::findOrFail($id);
                $pouzivatel->telefon = $request->get('telefon_pouzivatel');
                $pouzivatel->save();

            }

        }


        $inzerat->save();

        //  return redirect()->action('RealitkaInzeratyController@index');
        return redirect()->action('InzeratyController@show', $inzerat->id);

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
