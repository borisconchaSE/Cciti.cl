function formato_rut(rut) {
    if (rut === '' || rut === 0 || rut === null || rut === undefined) return '';

    rut = rut.replace(/-/gi, '');
    rut = rut.replace(/\./gi, '');
    rut = rut.replace(/\(/gi, '');
    rut = rut.replace(/\)/gi, '');
    rut = rut.replace(/\//gi, '');
    rut = rut.replace(/\\/gi, '');
    rut = rut.replace('k', 'K');

    var sRut1 = rut; // Contador de para saber cuando insertar el . o la -
    var nPos = 0; // Guarda el rut invertido con los puntos y el guión agregado
    var sInvertido = ''; // Guarda el resultado final del rut como debe ser
    var sRut = '';
    for (var i = sRut1.length - 1; i >= 0; i--) {
        sInvertido += sRut1.charAt(i);
        if (i == sRut1.length - 1) {
            sInvertido += '-';
        } else if (nPos == 3) {
            sInvertido += '.';
            nPos = 0;
        }
        nPos++;
    }
    for (var j = sInvertido.length - 1; j >= 0; j--) {
        if (sInvertido.charAt(sInvertido.length - 1) != '.')
            sRut += sInvertido.charAt(j);
        else if (j != sInvertido.length - 1)
            sRut += sInvertido.charAt(j);
    }
    //Pasamos al campo el valor formateado
    rut = sRut.toUpperCase();
    return rut;
}

function Valida_Rut(Objeto) {
    var tmpstr = "";
    var intlargo = Objeto
    if (intlargo.length > 0) {
        crut = Objeto
        largo = crut.length;
        if (largo < 2) {
            alert('rut inválido')
            return false;
        }
        for (i = 0; i < crut.length; i++)
            if (crut.charAt(i) != ' ' && crut.charAt(i) != '.' && crut.charAt(i) != '-') {
                tmpstr = tmpstr + crut.charAt(i);
            }
        rut = tmpstr;
        crut = tmpstr;
        largo = crut.length;

        if (largo > 2)
            rut = crut.substring(0, largo - 1);
        else
            rut = crut.charAt(0);

        dv = crut.charAt(largo - 1);

        if (rut == null || dv == null)
            return 0;

        var dvr = '0';
        suma = 0;
        mul = 2;

        for (i = rut.length - 1; i >= 0; i--) {
            suma = suma + rut.charAt(i) * mul;
            if (mul == 7)
                mul = 2;
            else
                mul++;
        }

        res = suma % 11;
        if (res == 1)
            dvr = 'k';
        else if (res == 0)
            dvr = '0';
        else {
            dvi = 11 - res;
            dvr = dvi + "";
        }

        if (dvr != dv.toLowerCase()) {
            return false;
        }
        return true;
    }
}

function valida_rut(rutCompleto) {
    if (!/^[0-9]+-[0-9kK]{1}$/.test(rutCompleto)) return false;
    var tmp = rutCompleto.split('-');
    if (tmp[1] == 'K') tmp[1] = 'k';
    return (dig_v(tmp[0])) == tmp[1];
}

function dig_v(T) {
    var M = 0, S = 1;
    for (; T; T = Math.floor(T / 10)) {
        S = (S + T % 10 * (9 - M++ % 6)) % 11;
    }
    return S ? S - 1 : 'k';
}

// Peru DNI
function nif(dni) {
    numero = dni.substr(0, dni.length - 1);
    lett = dni.substr(dni.length - 1, 1);
    numero = numero % 23;
    letra = 'TRWAGMYFPDXBNJZSQVHLCKET';
    letra = letra.substring(numero, numero + 1);
    if (letra != lett) {
        alert('Dni erroneo');
    }
}
