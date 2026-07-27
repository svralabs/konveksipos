<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-2 border-b border-slate-100 dark:border-zinc-800 pb-4 mb-6">
    <!-- Left Side -->
    <div class="flex items-center gap-4 flex-1">
        @if($showSearch)
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <svg class="h-[15px] w-[15px] text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" placeholder="Cari transaksi atau tugas..."
                    class="w-full pl-10 pr-12 py-[7px] bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-full text-[13px] font-medium text-slate-600 dark:text-zinc-300 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs" />
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <span class="px-1.5 py-0.5 text-[9px] font-bold text-slate-400 bg-slate-50 dark:bg-zinc-700 rounded border border-slate-200 dark:border-zinc-600 font-mono">⌘ F</span>
                </span>
            </div>
        @elseif($title)
            <h1 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">{{ $title }}</h1>
        @endif
    </div>

    <!-- Right Side -->
    <div class="flex items-center gap-3 self-end md:self-auto">

        @if($showDateFilter)
        <div
            x-data="{
                open: false,
                px: 0, py: 0,
                isDark: false,
                activePreset: '{{ $datePreset }}',
                startDate: '{{ $customStart }}',
                endDate: '{{ $customEnd }}',
                hoverDate: null,
                todayStr: '',
                ly: 0, lm: 0, ry: 0, rm: 0,
                presets: [
                    { key:'today',        label:'Hari Ini'        },
                    { key:'last_7_days',  label:'7 Hari Terakhir' },
                    { key:'last_30_days', label:'30 Hari Terakhir'},
                    { key:'this_month',   label:'Bulan Ini'       },
                    { key:'all_time',     label:'Semua Waktu'     }
                ],
                ld:[], rd:[],

                init(){
                    const n=new Date();
                    this.todayStr=this.iso(n);
                    this.ly=n.getFullYear(); this.lm=n.getMonth();
                    this.syncR(); this.build();
                    this.isDark = document.documentElement.classList.contains('dark');
                    new MutationObserver(()=>{
                        this.isDark = document.documentElement.classList.contains('dark');
                    }).observe(document.documentElement,{attributes:true,attributeFilter:['class']});
                },
                syncR(){ this.rm=this.lm+1; this.ry=this.ly; if(this.rm>11){this.rm=0;this.ry++;} },
                build(){ this.ld=this.days(this.ly,this.lm); this.rd=this.days(this.ry,this.rm); },
                days(y,m){
                    const r=[],dow=new Date(y,m,1).getDay(),tot=new Date(y,m+1,0).getDate();
                    for(let i=0;i<dow;i++){const d=new Date(y,m,-dow+i+1);r.push({n:d.getDate(),d:this.iso(d),cur:false});}
                    for(let i=1;i<=tot;i++){const d=new Date(y,m,i);r.push({n:i,d:this.iso(d),cur:true});}
                    while(r.length<42){const d=new Date(y,m+1,r.length-dow-tot+1);r.push({n:d.getDate(),d:this.iso(d),cur:false});}
                    return r;
                },
                iso(d){return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');},
                lbl(y,m){return new Date(y,m,1).toLocaleString('en-US',{month:'long',year:'numeric'});},
                prev(){if(--this.lm<0){this.lm=11;this.ly--;}this.syncR();this.build();},
                next(){if(++this.lm>11){this.lm=0;this.ly++;}this.syncR();this.build();},
                pick(ds){
                    this.activePreset='custom';
                    if(!this.startDate||(this.startDate&&this.endDate)){this.startDate=ds;this.endDate=null;}
                    else{if(ds<this.startDate){this.endDate=this.startDate;this.startDate=ds;}else{this.endDate=ds;}}
                },
                preset(key){ this.activePreset=key; },
                apply(){
                    if(this.activePreset === 'custom'){
                        const s=this.startDate||this.todayStr;
                        $wire.saveDateRange(s, this.endDate||s, 'custom');
                    } else {
                        $wire.setPreset(this.activePreset);
                    }
                    this.open=false;
                },
                toggle(btn){
                    const r=btn.getBoundingClientRect();
                    this.py=r.bottom+6; this.px=r.right;
                    this.open=!this.open;
                },
                cellSt(day){
                    const dk=this.isDark;
                    const d=day.d, s=this.startDate, e=this.endDate;
                    const eff=e||(!e&&this.hoverDate&&s&&this.hoverDate>s?this.hoverDate:null);
                    if(d===s||d===e) return 'background:#059669;color:#fff;font-weight:700;border-radius:8px;';
                    if(s&&eff&&d>s&&d<eff) return dk
                        ? 'background:#064e3b;color:#6ee7b7;font-weight:600;border-radius:0;'
                        : 'background:#d1fae5;color:#065f46;font-weight:600;border-radius:0;';
                    if(d===this.todayStr&&day.cur) return dk
                        ? 'color:#34d399;font-weight:800;border-radius:8px;border:1.5px solid #065f46;'
                        : 'color:#059669;font-weight:800;border-radius:8px;border:1.5px solid #6ee7b7;';
                    if(!day.cur) return dk ? 'color:#3f3f46;font-weight:400;' : 'color:#cbd5e1;font-weight:400;';
                    return dk ? 'color:#d4d4d8;font-weight:500;' : 'color:#334155;font-weight:500;';
                }
            }"
            x-init="init()"
            @keydown.escape.window="open=false"
        >
            <!-- Trigger Button -->
            <button
                @click="toggle($el)"
                type="button"
                :style="isDark
                    ? 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:rgba(6,78,59,0.4);border:1.5px solid #065f46;border-radius:9999px;font-size:13px;font-weight:700;color:#6ee7b7;white-space:nowrap;cursor:pointer;transition:background .15s;flex-shrink:0;'
                    : 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#ecfdf5;border:1.5px solid #a7f3d0;border-radius:9999px;font-size:13px;font-weight:700;color:#065f46;white-space:nowrap;cursor:pointer;transition:background .15s;flex-shrink:0;'"
            >
                <svg style="width:15px;height:15px;flex-shrink:0;" :style="isDark ? 'color:#34d399;' : 'color:#059669;'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <span style="white-space:nowrap;">{{ session('global_date_filter.label', 'Bulan Ini') }}</span>
                <svg style="width:12px;height:12px;flex-shrink:0;opacity:0.7;transition:transform .2s;" :style="open?'transform:rotate(180deg)':''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>

            <!-- Dropdown Panel -->
            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 translate-y-1"
                :style="`position:fixed;top:${py}px;right:${window.innerWidth-px}px;z-index:99999;`"
                @click.outside="open=false"
            >
                <!-- Card -->
                <div
                    :style="isDark
                        ? 'display:flex;flex-direction:row;background:#18181b;border:1px solid #3f3f46;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.4);overflow:hidden;width:680px;max-width:calc(100vw - 24px);'
                        : 'display:flex;flex-direction:row;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.10);overflow:hidden;width:680px;max-width:calc(100vw - 24px);'"
                >
                    <!-- LEFT: Preset list + Terapkan -->
                    <div
                        :style="isDark
                            ? 'width:172px;min-width:172px;flex-shrink:0;padding:18px 14px;border-right:1px solid #3f3f46;display:flex;flex-direction:column;justify-content:space-between;background:#18181b;'
                            : 'width:172px;min-width:172px;flex-shrink:0;padding:18px 14px;border-right:1px solid #f1f5f9;display:flex;flex-direction:column;justify-content:space-between;background:#ffffff;'"
                    >
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <template x-for="item in presets" :key="item.key">
                                <button
                                    @click="preset(item.key)"
                                    type="button"
                                    style="width:100%;text-align:left;border:none;cursor:pointer;font-size:13px;padding:9px 11px;border-radius:8px;transition:background .12s,color .12s;line-height:1.2;"
                                    :style="activePreset===item.key
                                        ? (isDark ? 'background:#064e3b;color:#6ee7b7;font-weight:700;border-radius:8px;' : 'background:#d1fae5;color:#065f46;font-weight:700;border-radius:8px;')
                                        : (isDark ? 'background:transparent;color:#a1a1aa;font-weight:500;border-radius:8px;' : 'background:transparent;color:#475569;font-weight:500;border-radius:8px;')"
                                ><span x-text="item.label"></span></button>
                            </template>
                        </div>
                        <button
                            @click="apply()"
                            type="button"
                            style="width:100%;padding:10px 0;background:#059669;color:#fff;font-size:13px;font-weight:700;border:none;border-radius:9px;cursor:pointer;margin-top:14px;transition:background .15s;"
                        >Terapkan</button>
                    </div>

                    <!-- RIGHT: Dual month calendar -->
                    <div
                        :style="isDark
                            ? 'flex:1;min-width:0;padding:18px 20px 16px;display:flex;flex-direction:column;gap:12px;background:#18181b;'
                            : 'flex:1;min-width:0;padding:18px 20px 16px;display:flex;flex-direction:column;gap:12px;background:#ffffff;'"
                    >
                        <!-- Month navigation -->
                        <div style="display:flex;align-items:center;gap:8px;">
                            <button @click="prev()" type="button"
                                style="width:28px;height:28px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:none;background:transparent;cursor:pointer;border-radius:7px;transition:background .12s;"
                                :style="isDark ? 'color:#a1a1aa;' : 'color:#64748b;'"
                                :class="isDark ? 'hover:!bg-zinc-700' : 'hover:!bg-slate-100'">
                                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                            </button>

                            <div style="flex:1;display:flex;justify-content:space-around;">
                                <span x-text="lbl(ly,lm)"
                                    style="font-size:13px;font-weight:800;text-align:center;flex:1;"
                                    :style="isDark ? 'color:#f4f4f5;' : 'color:#1e293b;'"></span>
                                <span x-text="lbl(ry,rm)"
                                    style="font-size:13px;font-weight:800;text-align:center;flex:1;"
                                    :style="isDark ? 'color:#f4f4f5;' : 'color:#1e293b;'"></span>
                            </div>

                            <button @click="next()" type="button"
                                style="width:28px;height:28px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:none;background:transparent;cursor:pointer;border-radius:7px;transition:background .12s;"
                                :style="isDark ? 'color:#a1a1aa;' : 'color:#64748b;'"
                                :class="isDark ? 'hover:!bg-zinc-700' : 'hover:!bg-slate-100'">
                                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </button>
                        </div>

                        <!-- Dua bulan side by side — render eksplisit -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                            <!-- Bulan kiri -->
                            <div>
                                <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;margin-bottom:4px;">
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Su</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Mo</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Tu</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">We</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Th</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Fr</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Sa</span>
                                </div>
                                <div style="display:grid;grid-template-columns:repeat(7,1fr);">
                                    <template x-for="(day,i) in ld" :key="'L'+i">
                                        <button @click="pick(day.d)" @mouseenter="hoverDate=day.d" @mouseleave="hoverDate=null"
                                            type="button"
                                            style="height:33px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;font-size:12.5px;transition:all .08s;width:100%;"
                                            :style="cellSt(day)">
                                            <span x-text="day.n"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Bulan kanan -->
                            <div>
                                <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;margin-bottom:4px;">
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Su</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Mo</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Tu</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">We</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Th</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Fr</span>
                                    <span style="font-size:10.5px;font-weight:700;color:#64748b;padding:2px 0;">Sa</span>
                                </div>
                                <div style="display:grid;grid-template-columns:repeat(7,1fr);">
                                    <template x-for="(day,i) in rd" :key="'R'+i">
                                        <button @click="pick(day.d)" @mouseenter="hoverDate=day.d" @mouseleave="hoverDate=null"
                                            type="button"
                                            style="height:33px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;font-size:12.5px;transition:all .08s;width:100%;"
                                            :style="cellSt(day)">
                                            <span x-text="day.n"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endif

        <!-- Divider -->
        <div class="w-px h-5 bg-slate-200 dark:bg-zinc-700 mx-1"></div>

        <!-- Mail -->
        <button class="w-[30px] h-[30px] flex items-center justify-center bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700/50 rounded-full text-slate-500 dark:text-zinc-400 transition shadow-xs cursor-pointer">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 17.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
        </button>

        <!-- Bell -->
        <button class="relative w-[30px] h-[30px] flex items-center justify-center bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700/50 rounded-full text-slate-500 dark:text-zinc-400 transition shadow-xs cursor-pointer">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
            <span class="absolute top-[5px] right-[5px] w-[6px] h-[6px] bg-rose-500 rounded-full border-[1.5px] border-white dark:border-zinc-800"></span>
        </button>

        <!-- Profile -->
        <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200 dark:border-zinc-800">
            <div class="w-[30px] h-[30px] rounded-full bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center font-bold text-emerald-800 dark:text-emerald-300 shadow-xs border border-emerald-200 dark:border-emerald-900 overflow-hidden" style="font-size:11px;">
                @if(auth()->check() && auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="w-full h-full object-cover">
                @else
                    {{ auth()->check() ? substr(auth()->user()->name, 0, 2) : 'AD' }}
                @endif
            </div>
            <div class="hidden sm:block text-left">
                <p class="font-bold text-slate-800 dark:text-zinc-200 leading-none" style="font-size:13px;">{{ auth()->check() ? auth()->user()->name : 'Super Admin' }}</p>
                <p class="text-slate-400 dark:text-zinc-500 mt-0.5 leading-none" style="font-size:10px;">{{ auth()->check() ? auth()->user()->email : 'superadmin@konveksipos.com' }}</p>
            </div>
        </div>

    </div>
</div>
