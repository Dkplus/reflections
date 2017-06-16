// This file has been originally copied from https://github.com/FabioBatSilva/annotations/blob/2.0/src/Parser/grammar.pp 
// Full credits go to Fabio B. Silva
%skip   space               [\x20\x09\x0a\x0d]+
%skip   doc_                (\/\*\*)
%skip   _doc                (\*\/)
%token  star                [*]

%token  at                  @                                   -> annot
%token  fullstop_text       ((?!(@|\*\/)).)+\.
%token  text                ((?!(@|\*\/)).)+

%token  annot:identifier    [\\]?[a-zA-Z_][\\a-zA-Z0-9_\-]*     -> values

%skip   values:star         [*]
%skip   values:_doc         [*/]
%skip   values:space        [\x20\x09\x0a\x0d]+
%token  values:comma        ,                           -> value
%token  values:at           @                           -> annot
%token  values:brace_       {                           -> value
%token  values:_brace       }                           -> value
%token  values:parenthesis_ \(                          -> value
%token  values:_parenthesis \)                          -> default
%token  values:text         ((?!(\W@|\*\/)).)+          -> default

%skip   value:star          [*]
%skip   value:_doc          [*/]
%skip   value:space         [\x20\x09\x0a\x0d]+
%token  value:_parenthesis  \)                          -> values
%token  value:at            @                           -> annot
%token  value:null          null
%token  value:boolean       false|true
%token  value:identifier    [\\a-zA-Z_][\\a-zA-Z0-9_\-]*
%token  value:brace_        {
%token  value:_brace        }
%token  value:colon         :
%token  value:comma         ,
%token  value:equals        =
%token  value:number        \-?(0|[1-9]\d*)(\.\d+)?([eE][\+\-]?\d+)?

%token  value:string        "(.*?)(?<!\\)"

#docblock:
    just_summary()
  | annotations()?
  | summary() description() annotations()?

just_summary:
    ( ::star::? <text> )* (::star:: <fullstop_text>)? #summary

#summary:
    ::star:: <fullstop_text> ::star::*
  | ( ::star:: <text> )+ ::star:: ::star::+
  | ::star:: ( <text> ::star:: )+ <fullstop_text> ::star::*

#description:
    paragraph()* last_paragraph()?

#paragraph:
    (::star::? text())+ ::star:: ::star::+

last_paragraph:
    (::star::? text())+ #paragraph 

#annotations:
    (::star::? annotation())+

#annotation:
    ::at:: identifier() comments()
  | ::at:: identifier() ( parameters() | comments()+ )?

#comments:
    ::star::? text()+

#values:
    value() ( ::comma:: value() )* ::comma::?

#list:
    ::brace_:: ( (value() ( ::comma:: value() )*) ::comma::? )? ::_brace::

#map:
    ::brace_:: pairs() ::comma::? ::_brace::

#pairs:
    pair() ( ::comma:: pair() )*

#pair:
    (identifier() | string() | number() | constant()) ( ::equals:: | ::colon:: ) value()

#value:
    <boolean> | <null> | string() | map() | list() | number() | pair() | annotation() | constant()

parameters:
    ( ::parenthesis_:: ( values() )? ::_parenthesis:: ) | string()?

identifier:
    <identifier>

#constant:
    <identifier> (<colon> <colon> <identifier>)?

string:
    <string>

text:
    <text>
  | <fullstop_text>

number:
    <number>

