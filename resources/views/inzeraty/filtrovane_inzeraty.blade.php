@extends('layouts.app')

@section('content')


    <div class="col-sm-3 col-md-3 col-lg-3 pull-left">

        <div>
            <div align="center">
                <h4>VYHĽADÁVANIE</h4>
            </div>

            <form action="{{route('inzeraty.index')}}" method="get">
                {{csrf_field()}}

                <input type="hidden" id="ID" name="obec_id"/>

                <label for="lokalita">Obec/Mesto</label>
                <input list="obce" id="lokalita" class="form-control" placeholder="Zadajte lokalitu" name="lokalita"
                       autocomplete="off"/>
                <datalist id="obce">
                    @foreach($obce as $obec)
                        <option href="#" id="{{$obec->id}}">{{$obec->obec}}, okres {{$obec->okres_id}}</option>
                    @endforeach
                </datalist>

                <div class="form-group">
                    {{--<label for="kraj">Kraj</label>--}}
                    {{--<select id="kraj" class="form-control" name="kraj">--}}
                    {{--<option value="1">Bratislavský</option>--}}
                    {{--<option value="2">Trnavský</option>--}}
                    {{--<option value="3">Trenčiansky</option>--}}
                    {{--<option value="3">Nitriansky</option>--}}
                    {{--<option value="3">Žilinský</option>--}}
                    {{--<option value="3">Banskobystrický</option>--}}
                    {{--<option value="3">Prešovský</option>--}}
                    {{--<option value="3">Košický</option>--}}
                    {{--</select>--}}

                    <label for="kategoria">Kategória</label>
                    <select id="kategoria" class="form-control" name="kategoria">
                        <option value="1">Všetky nehnuteľnosti</option>
                        <option value="2">Ponuka real. kancelárií</option>
                        <option value="3">Súkromná inzercia</option>
                    </select>

                    <label for="typ">Typ</label>
                    <select id="typ" class="form-control" name="typ">
                        <option value="1">Predaj</option>
                        <option value="2">Prenájom</option>
                        <option value="3">Kúpa</option>
                        <option value="4">Podnájom</option>
                        <option value="5">Výmena</option>
                        <option value="6">Dražba</option>
                    </select>

                    <label for="druh">Druh</label>
                    <select class="form-control" id="druh" name="druh">
                        <option value="1">Všetko</option>
                        <optgroup label="BYTY">
                            <option value="101">Garsónka</option>
                            <option value="102">1 izbový byt</option>
                            <option value="103">2 izbový byt</option>
                            <option value="104">3 izbový byt</option>
                            <option value="105">4 izbový byt</option>
                            <option value="106">5 a viac izbový byt</option>
                            <option value="107">Mezonet</option>
                            <option value="108">Apartmán</option>
                            <option value="109">Iný byt</option>
                            <option value="110">Všetky byty</option>
                        </optgroup>
                        <optgroup label="DOMY">
                            <option value="201">Chata</option>
                            <option value="202">Chalupa</option>
                            <option value="203">Rodinný dom</option>
                            <option value="204">Rodinná vila</option>
                            <option value="205">Bývalá poľnohosp. usadlosť</option>
                            <option value="206">Iný objekt na bývanie a rekreáciu</option>
                            <option value="207">Všetky domy</option>
                        </optgroup>
                        <optgroup label="PRIESTORY">
                            <option value="301">Kancelárie, administratívne priestory</option>
                            <option value="302">Obchodné priestory</option>
                            <option value="303">Reštauračné priestory</option>
                            <option value="304">Športové priestory</option>
                            <
                            <option value="305">Iné komerčné priestory</option>
                            <option value="306">Priestor pre výrobu</option>
                            <option value="307">Priestor pre sklad</option>
                            <option value="308">Opravárenský priestor</option>
                            <option value="309">Priestor pre chov zvierat</option>
                            <option value="310">Iný prevádzkovy priestor</option>
                            <option value="311">Všetky priestory</option>
                        </optgroup>
                        <optgroup label="POZEMKY">
                            <option value="401">Rekreačný pozemok</option>
                            <option value="402">Pozemok pre rodinné domy</option>
                            <option value="403">Pozemok pre bytovú výstavbu</option>
                            <option value="404">Komerčná zóna</option>
                            <option value="405">Priemyselná zóna</option>
                            <option value="406">Záhrada</option>
                            <option value="407">Sad</option>
                            <option value="407">Lúka, pasienok</option>
                            <option value="408">Orná poda</option>
                            <option value="409">Chmelnica, vinica</option>
                            <option value="410">Lesná pôda</option>
                            <option value="411">Vodná plocha</option>
                            <option value="412">Iný poľnohosp. pozemok</option>
                            <option value="413">Hrobové miesto</option>
                            <option value="414">Všetky pozemky</option>
                        </optgroup>
                    </select>
                    <label for="stav">Stav</label>
                    <select id="stav" class="form-control" name="stav">
                        <option value="1">Všetky stavy</option>
                        <option value="2">Novostavba</option>
                        <option value="3">Čiastočná rekonštrukcia</option>
                        <option value="4">Kompletná rekonštrukcia</option>
                        <option value="5">Pôvodný stav</option>
                        <option value="6">Vo výstavbe</option>
                        <option value="7">developerský projekt</option>
                    </select>
                    <label for="cena">Cena(€)</label>
                    <div class="input-group" id="cena">
                        <input placeholder="od" class="form-control" type="number" min="0" name="cena_od"/>
                        <span class="input-group-addon"></span>
                        <input placeholder="do" class="form-control" type="number" min="0" name="cena_do"/>
                    </div>
                    <label for="vymera">Výmera (m<sup>2</sup>)</label>
                    <div class="input-group" id="vymera">
                        <input placeholder="od" class="form-control" type="number" min="0" name="vymera_od"/>
                        <span class="input-group-addon"></span>
                        <input placeholder="do" class="form-control" type="number" min="0" name="vymera_do"/>
                    </div>

                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-danger form-control" value="VYHĽADAŤ"/>
                </div>
                @include('errors')
            </form>


        </div>

        @if(($widget)->count())
            <div align="center" style="background:#EEEEEE; border-radius: 2%; font-family: Calibri">
                </br>
                <h1 style="color: #FFAF0A"><i class="fas fa-star"></i> <i class="fas fa-star"></i> <i
                            class="fas fa-star"></i></h1>

                @foreach($widget as $inzerat)
                    <a style="color: black" href="/inzeraty/{{$inzerat->id}}">
                        <strong>{{$inzerat->obec->obec}}</strong></br>
                        <strong>{{$inzerat->cena}} €</strong></br>
                        <img src="{{$inzerat->obrazok}}"
                             style="height:70%;width: 70%;border-radius: 4%"/></br></br>
                    </a>
                    <hr>
                @endforeach
            </div>
        @endif

    </div>

    @if($inzeraty)
        @foreach($inzeraty as $inzerat)
            <a style="color: black" href="/inzeraty/{{$inzerat->id}}">
                <div class="col-md-9 col-lg-9 col-sm-9 pull-right">

                    <div class="col-md-4 col-lg-4 col-sm-4 image-container">
                        <img src="{{$inzerat->obrazok}}"
                             style="height:90%;width: 90%;margin-left:-15px;min-height: 150px;border-radius: 4%"/>
                    </div>
                    <div class="excerpt">
                        <h4 class="heading" style="font-family: Calibri">{{$inzerat->nazov}}</h4>
                        <ul class="nospace meta">
                            <li>
                                <i class="fas fa-home"></i>{{$inzerat->druh->podnazov}}, {{$inzerat->obec->obec}}
                            </li>


                            @if($inzerat->crawler!=true)
                                @if ($inzerat->stav != null)
                                    <li><i class="fas fa-building"></i> {{$inzerat->stav->nazov}}
                                        , {{$inzerat->vymera_domu}}
                                        m²
                                    </li>
                                @else
                                    <li><i class="fas fa-building"></i> {{$inzerat->vymera_domu}}m²</li>
                                @endif
                            @endif


                            <li><i class="fas fa-hand-paper"></i> {{$inzerat->typ->nazov}} </li>

                            @if ($inzerat->cena == null)
                                <li><i class="fas fa-euro-sign"></i> <span style="color: limegreen">Dohodou</span>
                                </li>
                            @else
                                <li><i class="fas fa-euro-sign"></i> <span
                                            style="color: limegreen">{{$inzerat->cena}}</span></li>
                            @endif

                        </ul>
                        <p style="color:#585858">
                            {{substr($inzerat->popis,0,300)}}...
                        </p>
                        {{--<p class="pull-right">Pocet zobrazeni: {{$inzerat->pocet_zobrazeni}}x</p>--}}


                    </div>
                    <hr/>
                </div>
            </a>
        @endforeach
        <div>{{ $inzeraty->links() }}</div>
    @else
        <div class="col-md-9 col-lg-9 col-sm-9 pull-right">
            <div class="jumbotron">
                <h1>
                    <i class="fas fa-frown"></i>
                </h1>
                <p>Nenašli sa žiadne inzeráty</p>

            </div>
        </div>
    @endif



    <!-- <p><a class="btn btn-lg btn-success" href="#" role="button">Get started today</a></p> -->







    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src='{{ URL::asset('js/lokalita.js') }}'></script>
@endsection