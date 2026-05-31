/* =========================================================
   FM Theme — общий слой кастомизации
   ---------------------------------------------------------
   Применяется И на сайте (index.html), И в админке (admin.html).
   Настройки хранятся в localStorage['fm-theme'] как JSON.
   Меняешь тему в админке -> сайт обновляется (в т.ч. в другой
   вкладке, через событие 'storage').
   ========================================================= */
const FMTheme = (function(){
  const KEY = 'fm-theme';

  // Акцентные пресеты: accent / ink (тёмный) / soft / soft-2 (фоновые тинты)
  const ACCENTS = {
    terracotta:{label:'Терракота', accent:'#E4581F', ink:'#B8420F', soft:'#FBEADF', soft2:'#FCF2EA'},
    indigo:    {label:'Индиго',    accent:'#2A6FDB', ink:'#1B4FA8', soft:'#E6EEFB', soft2:'#F0F5FD'},
    emerald:   {label:'Изумруд',   accent:'#1F8A5B', ink:'#146443', soft:'#E3F2EB', soft2:'#F0F8F4'},
    violet:    {label:'Фиолетовый',accent:'#7A4FD0', ink:'#5A37A0', soft:'#EEE8FB', soft2:'#F5F1FD'},
    amber:     {label:'Янтарь',    accent:'#D9920B', ink:'#A66C06', soft:'#FBF0D6', soft2:'#FCF6E8'},
    graphite:  {label:'Графит',    accent:'#5B5048', ink:'#332E29', soft:'#ECE7DF', soft2:'#F4F0EA'}
  };

  // Тон фона
  const BGS = {
    warm:   {label:'Тёплый',      paper:'#F7F4EF', paper2:'#FBF9F5', line:'#E8E1D6', line2:'#F0EBE2'},
    neutral:{label:'Нейтральный', paper:'#F4F4F2', paper2:'#FAFAF8', line:'#E5E4DF', line2:'#EFEEEA'},
    cool:   {label:'Холодный',    paper:'#F1F4F6', paper2:'#F8FAFB', line:'#DFE4E8', line2:'#EBEFF1'}
  };

  // Скругление углов
  const RADII = {
    round:{label:'Скруглённые', r:'14px', lg:'22px', xl:'30px'},
    soft: {label:'Мягкие',      r:'10px', lg:'16px', xl:'22px'},
    sharp:{label:'Острые',      r:'4px',  lg:'6px',  xl:'9px'}
  };

  const defaults = {accent:'terracotta', bg:'warm', radius:'round'};

  function load(){
    let t = {};
    try{ t = JSON.parse(localStorage.getItem(KEY) || '{}'); }catch(e){}
    return Object.assign({}, defaults, t);
  }

  function apply(t, root){
    t = Object.assign({}, defaults, t || {});
    root = root || document.documentElement;
    const a = ACCENTS[t.accent] || ACCENTS.terracotta;
    root.style.setProperty('--accent', a.accent);
    root.style.setProperty('--accent-ink', a.ink);
    root.style.setProperty('--accent-soft', a.soft);
    root.style.setProperty('--accent-soft-2', a.soft2);
    const b = BGS[t.bg] || BGS.warm;
    root.style.setProperty('--paper', b.paper);
    root.style.setProperty('--paper-2', b.paper2);
    root.style.setProperty('--line', b.line);
    root.style.setProperty('--line-2', b.line2);
    const r = RADII[t.radius] || RADII.round;
    root.style.setProperty('--radius', r.r);
    root.style.setProperty('--radius-lg', r.lg);
    root.style.setProperty('--radius-xl', r.xl);
  }

  function save(t){
    try{ localStorage.setItem(KEY, JSON.stringify(t)); }catch(e){}
    apply(t);
  }

  function reset(){
    try{ localStorage.removeItem(KEY); }catch(e){}
    apply(defaults);
    return Object.assign({}, defaults);
  }

  // применяем сразу при подключении (до отрисовки body)
  apply(load());
  // живая синхронизация между вкладками
  window.addEventListener('storage', e=>{ if(e.key === KEY) apply(load()); });

  return { KEY, ACCENTS, BGS, RADII, defaults, load, apply, save, reset };
})();
try{ window.FMTheme = FMTheme; }catch(e){}
