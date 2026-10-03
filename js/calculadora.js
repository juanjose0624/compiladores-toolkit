(function(){
  const readout = document.getElementById('readout');
  const tape = document.getElementById('historyTape');
  const modeDeg = document.getElementById('mode-deg');
  const modeRad = document.getElementById('mode-rad');

  let expr = '';
  let angleMode = 'deg';
  let justEvaluated = false;

  const DISPLAY_MAP = {
    '*':'×', '/':'÷', 'pi':'π', 'sqrt(':'√(',
    'sin(':'sin(', 'cos(':'cos(', 'tan(':'tan(', 'log(':'log(', 'ln(':'ln('
  };

  function toDisplay(str){
    let out = str;
    out = out.replace(/\*/g,'×').replace(/\//g,'÷');
    return out;
  }

  function render(){
    readout.innerHTML = (expr === '' ? '0' : toDisplay(expr)) + '<span class="cursor"></span>';
    readout.scrollLeft = readout.scrollWidth;
  }

  function setMode(mode){
    angleMode = mode;
    modeDeg.classList.toggle('active', mode === 'deg');
    modeRad.classList.toggle('active', mode === 'rad');
  }
  modeDeg.addEventListener('click', () => setMode('deg'));
  modeRad.addEventListener('click', () => setMode('rad'));

  function errorFlash(msg){
    Swal.fire({
      title: 'Entrada inválida',
      text: msg || 'Revisa la expresión.',
      icon: 'error',
      background: '#13161b',
      color: '#dfe4ea',
      confirmButtonColor: '#3ecf9b',
      confirmButtonText: 'Entendido',
      customClass: { popup: 'compas-popup' }
    });
  }

  function buildEvalExpr(raw){
    let e = raw;
    // factorial: turn "N!" into "factorial(N)" for simple trailing numbers/parens
    e = e.replace(/(\d+(\.\d+)?|\))!/g, 'factorial($1)');
    // x^2 shorthand appended directly after a number or ")"
    e = e.replace(/x\^2/g, '^2');

    if(angleMode === 'deg'){
      e = e.replace(/sin\(/g, 'sin((pi/180)*');
      e = e.replace(/cos\(/g, 'cos((pi/180)*');
      e = e.replace(/tan\(/g, 'tan((pi/180)*');
    }
    return e;
  }

  function evaluate(){
    if(expr.trim() === '') return;
    try{
      const evalExpr = buildEvalExpr(expr);
      const result = math.evaluate(evalExpr);
      const formatted = math.format(result, { precision: 12 });
      tape.textContent = toDisplay(expr) + ' =';
      expr = formatted;
      justEvaluated = true;
      render();
    }catch(err){
      errorFlash('No se pudo evaluar: ' + toDisplay(expr));
    }
  }

  function pressNum(v){
    if(justEvaluated){ expr = ''; justEvaluated = false; }
    expr += v;
    render();
  }
  function pressDot(){
    if(justEvaluated){ expr = ''; justEvaluated = false; }
    // avoid double dot in current number segment
    const seg = expr.split(/[^0-9.]/).pop();
    if(seg.includes('.')) return;
    expr += (seg === '' ? '0.' : '.');
    render();
  }
  function pressOp(v){
    if(justEvaluated){ justEvaluated = false; }
    if(expr === '' && v !== '-') return;
    expr += v;
    render();
  }
  function pressFn(v){
    if(justEvaluated){ expr = ''; justEvaluated = false; }
    if(v === '!'){
      expr += '!';
    } else if(v === 'x^2'){
      expr += '^2';
    } else {
      expr += v;
    }
    render();
  }
  function pressConst(v){
    if(justEvaluated){ expr = ''; justEvaluated = false; }
    expr += v;
    render();
  }
  function pressParen(which){
    if(justEvaluated){ expr = ''; justEvaluated = false; }
    expr += which === 'open' ? '(' : ')';
    render();
  }
  function backspace(){
    if(justEvaluated){ expr = ''; justEvaluated = false; render(); return; }
    expr = expr.slice(0, -1);
    render();
  }
  function clearAll(){
    expr = '';
    justEvaluated = false;
    tape.textContent = '\u00A0';
    render();
  }
  function sign(){
    if(expr === '') return;
    // wrap whole expr in unary minus toggle at start if simple, else wrap last number
    const match = expr.match(/(-?\d+(\.\d+)?)$/);
    if(match){
      const num = match[1];
      const start = expr.length - num.length;
      const negated = num.startsWith('-') ? num.slice(1) : '-' + num;
      expr = expr.slice(0, start) + negated;
      render();
    }
  }

  document.querySelectorAll('.key').forEach(key => {
    key.addEventListener('click', () => {
      const act = key.dataset.act;
      const val = key.dataset.val;
      switch(act){
        case 'num': pressNum(val); break;
        case 'dot': pressDot(); break;
        case 'op': pressOp(val); break;
        case 'fn': pressFn(val); break;
        case 'const': pressConst(val); break;
        case 'paren-open': pressParen('open'); break;
        case 'paren-close': pressParen('close'); break;
        case 'backspace': backspace(); break;
        case 'clear': clearAll(); break;
        case 'clear-entry': clearAll(); break;
        case 'sign': sign(); break;
        case 'equals': evaluate(); break;
      }
    });
  });

  // keyboard support (solo aplica mientras el módulo de calculadora está activo)
  window.addEventListener('keydown', (e) => {
    const calcModule = document.getElementById('module-calc');
    if(!calcModule || !calcModule.classList.contains('active')) return;

    if(/[0-9]/.test(e.key)){ pressNum(e.key); return; }
    if(e.key === '.'){ pressDot(); return; }
    if(['+','-','*','/'].includes(e.key)){ pressOp(e.key); return; }
    if(e.key === '('){ pressParen('open'); return; }
    if(e.key === ')'){ pressParen('close'); return; }
    if(e.key === 'Enter' || e.key === '='){ e.preventDefault(); evaluate(); return; }
    if(e.key === 'Backspace'){ backspace(); return; }
    if(e.key === 'Escape'){ clearAll(); return; }
  });

  clearAll();


  /* =========================================================
     SELECTOR DE MÓDULOS (Calculadora / Referencia / Bases)
     ========================================================= */
  const switchButtons = document.querySelectorAll('.mswitch-btn');
  const modules = document.querySelectorAll('.module');

  function activateModule(name){
    modules.forEach(m => m.classList.toggle('active', m.id === 'module-' + name));
    switchButtons.forEach(b => {
      const isActive = b.dataset.module === name;
      b.classList.toggle('active', isActive);
      b.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
  }

  switchButtons.forEach(btn => {
    btn.addEventListener('click', () => activateModule(btn.dataset.module));
  });


  /* =========================================================
     CONVERSOR DE BASES (DEC / HEX / OCT / BIN)
     ========================================================= */
  const baseInputs = {
    10: document.getElementById('base-dec'),
    16: document.getElementById('base-hex'),
    8:  document.getElementById('base-oct'),
    2:  document.getElementById('base-bin')
  };

  const VALID_CHARS = {
    10: /^[0-9]*$/,
    16: /^[0-9a-fA-F]*$/,
    8:  /^[0-7]*$/,
    2:  /^[01]*$/
  };

  function clearInvalid(){
    Object.values(baseInputs).forEach(inp => inp.classList.remove('invalid'));
  }

  function syncFrom(base, rawValue){
    clearInvalid();

    if(rawValue.trim() === ''){
      Object.values(baseInputs).forEach(inp => { inp.value = ''; });
      return;
    }

    if(!VALID_CHARS[base].test(rawValue)){
      baseInputs[base].classList.add('invalid');
      return;
    }

    let decimalValue;
    try{
      decimalValue = BigInt(base === 10 ? rawValue : (base === 16 ? '0x' + rawValue : (base === 8 ? '0o' + rawValue : '0b' + rawValue)));
    }catch(err){
      baseInputs[base].classList.add('invalid');
      return;
    }

    Object.entries(baseInputs).forEach(([b, inp]) => {
      const bNum = Number(b);
      if(bNum === base) return; // no tocar el campo donde el usuario está escribiendo
      inp.value = decimalValue.toString(bNum).toUpperCase();
    });
  }

  Object.entries(baseInputs).forEach(([base, input]) => {
    if(!input) return;
    input.addEventListener('input', () => {
      syncFrom(Number(base), input.value.trim());
    });
  });

  const baseClearBtn = document.querySelector('[data-act="base-clear"]');
  if(baseClearBtn){
    baseClearBtn.addEventListener('click', () => {
      Object.values(baseInputs).forEach(inp => { inp.value = ''; inp.classList.remove('invalid'); });
    });
  }


  /* =========================================================
     COPIAR RESULTADO AL PORTAPAPELES
     ========================================================= */
  const copyBtn = document.getElementById('copyResult');
  if(copyBtn){
    copyBtn.addEventListener('click', async () => {
      const value = expr === '' ? '0' : expr;
      try{
        await navigator.clipboard.writeText(value);
        const original = copyBtn.innerHTML;
        copyBtn.classList.add('copied');
        copyBtn.innerHTML = '<span class="copy-icon">✓</span> Copiado';
        setTimeout(() => {
          copyBtn.classList.remove('copied');
          copyBtn.innerHTML = original;
        }, 1400);
      }catch(err){
        errorFlash('No se pudo copiar al portapapeles.');
      }
    });
  }


  /* =========================================================
     TEMA CLARO / OSCURO
     ========================================================= */
  const themeToggle = document.getElementById('themeToggle');
  const THEME_KEY = 'compas-calc-theme';

  function applyTheme(theme){
    document.body.setAttribute('data-theme', theme);
    try{ localStorage.setItem(THEME_KEY, theme); }catch(err){ /* almacenamiento no disponible, se ignora */ }
  }

  let savedTheme = 'dark';
  try{ savedTheme = localStorage.getItem(THEME_KEY) || 'dark'; }catch(err){ /* modo privado u otro bloqueo */ }
  applyTheme(savedTheme);

  if(themeToggle){
    themeToggle.addEventListener('click', () => {
      const current = document.body.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
      applyTheme(current === 'light' ? 'dark' : 'light');
    });
  }

})();