	<section id="chap-refine" class="chapter">
		<h2>Refinement</h2>
		<p>Beyond assessing the quality of a knowledge graph, there exist techniques to <em>refine</em> the knowledge graph, in particular to (semi-)automatically complete and correct the knowledge graph&nbsp;<?php echo $references->cite("Paulheim17"); ?>, aka <em>knowledge graph completion</em> and <em>knowledge graph correction</em>, respectively. As distinguished from the creation and enrichment tasks outlined in Chapter&nbsp;<?php echo ref("chap:create"); ?>, refinement typically does not involve applying extraction or mappings over external sources in order to ingest their content into a given knowledge graph (potentially using external sources to verify its content).</p>

		<section id="ssec-completion" class="section">
		<h3>Completion</h3>
		<p>Knowledge graphs are characterised by incompleteness&nbsp;<?php echo $references->cite("West14"); ?>. As such, knowledge graph completion aims at filling in the <em>missing edges</em> (aka <em>missing links</em>) of a knowledge graph, i.e., edges that are deemed correct but are neither given nor entailed by the knowledge graph. This task is often addressed with <em>link prediction</em> techniques proposed in the area of <em>Statistical Relational Learning</em>&nbsp;<?php echo $references->cite("Getoor07"); ?>, which predict the existence – or sometimes more generally, predict the probability of correctness – of missing edges. For instance, one might predict that the edge <?php echo gedge("Moon&nbsp;Valley","bus","San&nbsp;Pedro"); ?> is a probable missing edge for the graph of Figure&nbsp;<?php echo ref("fig:chileTransport"); ?>, given that most bus routes observed are return services (i.e., <span class="gelab">bus</span> is typically symmetric). Link prediction may target three settings: <em>general links</em> involving edges with arbitrary labels, e.g., <span class="gelab">bus</span>, <span class="gelab">flight</span>, <span class="gelab">type</span>, etc.; <em>type links</em> involving edges with label <span class="gelab">type</span>, indicating the type of an entity; and <em>identity links</em> involving edges with label <span class="gelab">same as</span>, indicating that two nodes refer to the same entity (cf. Section&nbsp;<?php echo ref("sssec:external_identy"); ?>). While type and identity links can be addressed using general link prediction techniques, the particular semantics of type and identity links can be addressed with custom techniques. The related task of generating links across knowledge graphs – referred to as <em>link discovery</em>&nbsp;<?php echo $references->cite("nentwig2017survey"); ?> – will be discussed later in Section&nbsp;<?php echo ref("ssec:principles"); ?>.</p>

		<h4 id="sssec-general-link-prediction" class="subsection">General link prediction</h4>
		<p>Link prediction, in the general case, is often addressed with inductive techniques as discussed in Chapter&nbsp;<?php echo ref("chap:inductive"); ?>, and in particular, knowledge graph embeddings and rule/axiom mining. For example, given Figure&nbsp;<?php echo ref("fig:chileTransport"); ?>, using knowledge graph embeddings, we may detect that given an edge of the form <?php echo gedge("\(x\)","bus","\(y\)"); ?>, a (missing) edge <?php echo gedge("\(y\)","bus","\(x\)"); ?> has high plausibility, while using symbol-based approaches, we may learn the high-level rule <?php echo sedge("?x","bus",NULL,"?y","gvar"); ?> \(\Rightarrow\) <?php echo sedge("?y","bus",NULL,"?x","gvar"); ?> that may infer/predict new <span class="gelab">bus</span> links. Either approach would help us to predict the missing link <?php echo gedge("Moon Valley","bus","San Pedro"); ?>.</p>

		<h4 id="sssec-type-link-prediction" class="subsection">Type-link prediction</h4>
		<p>Type links are of particular importance to a knowledge graph, where dedicated techniques can be leveraged taking into account the specific semantics of such links. In the case of type prediction, there is only one edge label (<span class="gelab">type</span>) and typically fewer distinct values (classes) than in other cases, such that the task can be reduced to a traditional classification task&nbsp;<?php echo $references->cite("Paulheim17"); ?>, training models to identify each semantic class based on features such as outgoing and/or incoming edge labels on their instances in the knowledge graph&nbsp;<?php echo $references->cite("paulheim2013type,SleemanF13"); ?>. For example, assume that in Figure&nbsp;<?php echo ref("fig:chileTransport"); ?> we also know that <span class="gnode">Arica</span>, <span class="gnode">Calama</span>, <span class="gnode">Puerto Montt</span>, <span class="gnode">Punta Arenas</span> and <span class="gnode">Santiago</span> are of <span class="gelab">type</span> <span class="gnode">City</span>. We may then predict that <span class="gnode">Iquique</span> and <span class="gnode">Easter Island</span> are also of <span class="gelab">type</span> <span class="gnode">City</span> based on the presence of edges labelled <span class="gelab">flight</span> to/from these nodes, which (we assume) are learnt to be a good feature for prediction of that class (the former prediction is correct, while the latter is incorrect). Graph neural networks (see Section&nbsp;<?php echo ref("ssec:gnns"); ?>) can also be used for node classification/type prediction.</p>

		<h4 id="sssec-identity-link-prediction" class="subsection">Identity-link prediction</h4>
		<p>Predicting identity links involves searching for nodes that refer to the same entity, but are not stated or entailed to be the same; this is analogous to the task of <em>entity matching</em> (aka record linkage, deduplication, etc.) considered in more general data integration settings&nbsp;<?php echo $references->cite("KopckeR10"); ?>. Such techniques are generally based on two types of <em>matchers</em>: <em>value matchers</em> determine how similar the values of two entities on a given property are, which may involve similarity metrics on strings, numbers, dates, etc.; while <em>context matchers</em> consider the similarity of entities based on various nodes and edges&nbsp;<?php echo $references->cite("KopckeR10"); ?>. An illustrative example is given in Figure&nbsp;<?php echo ref("fig:identity"); ?>, where value matchers will compute similarity between values such as <span class="gnode">7400</span> and <span class="gnode">7500</span>, while context matchers will compute similarity between <span class="gnode">Easter Island</span> and <span class="gnode">Rapa&nbsp;Nui</span> based on their surrounding information, such as similar latitudes, longitudes, populations, and the same seat (conversely, a value matcher on this pair of nodes would measure string similarity between “<code>Easter Island</code>” and “<code>Rapa Ñui</code>”).</p>

		<figure id="fig-identity">
			<img src="images/fig-identity.svg" alt="Identity linking example: Easter Island and Rapa Nui denote the same place"/>
			<figcaption>Identity linking example: <span class="gnode">Easter&nbsp;Island</span> and <span class="gnode">Rapa&nbsp;Nui</span> denote the same place <a class="git" title="Consult the code for this example on Github" href="https://github.com/Knowledge-Graphs-Book/examples/blob/main/Chapter_8_Refinement/8_1_3_Identity-link_prediction/figure_8_1.ttl"></a></figcaption>
		</figure>

		<p>A major challenge in this setting is efficiency, where a pairwise matching would require \(O(n^2)\) comparisons for \(n\) the number of nodes. To address this issue, <em>blocking</em> can be used to group similar entities into (possibly overlapping, possibly disjoint) “blocks” based on similarity-preserving keys, with matching performed within each block&nbsp;<?php echo $references->cite("isele2011efficient,KopckeR10,DraisbachN11"); ?>; for example, if matching places based on latitude/longitude, blocks may represent geographic regions. An alternative to discrete blocking is to use <em>windowing</em> over entities in a similarity-preserving ordering&nbsp;<?php echo $references->cite("DraisbachN11"); ?>, or to consider searching for similar entities within <em>multi-dimensional spaces</em> (e.g., spacetime&nbsp;<?php echo $references->cite("santipantakis2019stld"); ?>, spaces with Minkowski distances&nbsp;<?php echo $references->cite("minkowski"); ?>, orthodromic spaces&nbsp;<?php echo $references->cite("orchid"); ?>, etc.&nbsp;<?php echo $references->cite("SherifN18"); ?>). The results can either be pairs of nodes with a computed confidence of them referring to the same entity, or crisp identity links extracted based on a fixed threshold, or binary classification&nbsp;<?php echo $references->cite("KopckeR10"); ?>. For confident identity links, the nodes’ edges may then be <em>consolidated</em>&nbsp;<?php echo $references->cite("HoganZUPD12"); ?>; for example, we may select <span class="gnode">Easter Island</span> as the canonical node and merge the edges of <span class="gnode">Rapa&nbsp;Nui</span> onto it, enabling us to find, e.g., <em>World Heritage Sites in the Pacific Ocean</em> from Figure&nbsp;<?php echo ref("fig:identity"); ?> based on the (consolidated) sub-graph <span class="gnode">World Heritage Site</span><?php echo etipl(); ?><span class="edge">named</span><?php echo esource() . gedge("Easter Island","ocean","Pacific"); ?>.</p>
		</section>

		<section id="ssec-correction" class="section">
		<h3>Correction</h3>
		<p>As opposed to completion – which finds new edges in a knowledge graph – correction identifies and removes existing incorrect edges in the knowledge graph. We here divide the principal approaches for knowledge graph correction into two main lines: <em>fact validation</em>, which assigns a plausibility score to a given edge, typically in reference to external sources; and <em>inconsistency repairs</em>, which aim to resolve inconsistencies found in the knowledge graph through ontological axioms.</p>

		<h4 id="sssec-fact-validation" class="subsection">Fact validation</h4>
		<p>The task of <em>fact validation</em> (aka <em>fact checking</em>)&nbsp;<?php echo $references->cite("gerber2015defacto,syed2018factcheck,yin2008truth,syed2019copaal,EstevesRRL18,shiralkar2017finding,shi2016discriminative,socher2013reasoning,bordes2013translating"); ?> involves assigning plausibility or <em>veracity</em> scores to facts/edges, typically between \(0\) and \(1\). An ideal fact-checking function assumes a hypothetical reference universe (an ideal knowledge graph) and would return \(1\) for the fact <?php echo gedge("Santa Lucía","city","Santiago"); ?> (being true) while returning \(0\) for <?php echo gedge("Sotomayor","city","Santiago"); ?> (being false). There is a clear relation between fact validation and link prediction – with both relying on assessing the plausibility of edges/facts/links – and indeed the same numeric- and symbol-based techniques can be applied for both cases. However, fact validation often considers online assessment of edges given as input, whereas link prediction is often an offline task that generates novel candidate edges to be assessed from the knowledge graph. Furthermore, works on fact validation are characterised by their consideration of external reference sources, which may be <em>unstructured sources</em>&nbsp;<?php echo $references->cite("gerber2015defacto,syed2018factcheck,Samadi2016,yin2008truth"); ?> or <em>structured sources</em> &nbsp;<?php echo $references->cite("syed2019copaal,shiralkar2017finding,shi2016discriminative,socher2013reasoning,bordes2013translating"); ?>.</p>
		<p>Approaches based on unstructured sources assume that they are given a <em>verbalisation function</em> – using, for example, rule-based approaches&nbsp;<?php echo $references->cite("ngonga2013sorry,ell2014sparql"); ?>, encoder–decoder architectures&nbsp;<?php echo $references->cite("gardent2017webnlg"); ?>, etc. – that is able to translate edges into natural language. Thereafter, approaches for computing the plausibility of facts in natural language – called <em>fact finders</em>&nbsp;<?php echo $references->cite("Pasternack2010,pasternack2011making"); ?> – can be directly employed. Many fact finding algorithms construct an \(n\)-partite (often bipartite) graph whose nodes are facts and sources, where a source is connected to a fact if the source “evidences” the fact, i.e., if it contains a text snippet that matches – with sufficient confidence – the verbalisation of the input edge. Two mutually-dependent scores, namely the trustworthiness of sources and the plausibility of facts, are then calculated based on this graph, where fact finders differ on how they compute these scores&nbsp;<?php echo $references->cite("pasternack2011making"); ?>. Here we mention three scores proposed by <?php echo $references->citet("Pasternack2010"); ?>:</p>
		<ul>
			<li><em>Sums</em>&nbsp;<?php echo $references->cite("Pasternack2010"); ?> adapts the classical HITS centrality algorithm&nbsp;<?php echo $references->cite("kleinberg1999hubs"); ?> by defining sources as hubs (with 0 authority score) and facts as authorities (with 0 hub score).</li>
			<li><em>Average Log</em>&nbsp;<?php echo $references->cite("Pasternack2010"); ?> extends HITS with a normalisation factor that prevents a single source from receiving a high trustworthiness score by evidencing many facts (that may be false).</li>
			<li><em>Investment</em>&nbsp;<?php echo $references->cite("Pasternack2010"); ?> lets the scores of facts grow with a non-linear function based on “investments” coming from the connected sources. The score a source receives from a fact is based on the individual facts in this particular source compared to the other connected sources.</li>
		</ul>
		<p><?php echo $references->citet("pasternack2011making"); ?> then show that these three algorithms can be generalised into a single multi-layered graph-based framework within which (1) a source can support a fact with a weight expressing uncertainty, (2) similar facts can support each other, and (3) sources can be grouped together leading to an implicit support between sources of the same group. Other approaches for fact checking of knowledge graphs later extended this framework&nbsp;<?php echo $references->cite("galland2010,Samadi2016"); ?>. Alternative approaches based on machine learning classifiers have also emerged, where commonly-used features include trust scores for information sources, co-occurrences of facts in sources, and so forth&nbsp;<?php echo $references->cite("gerber2015defacto,syed2018factcheck"); ?>.</p>
		<p>Approaches for fact validation based on structured data typically assume external knowledge graphs as reference sources and are based on finding paths that support the edge being validated. Unsupervised approaches search for undirected&nbsp;<?php echo $references->cite("shiralkar2017finding,ciampaglia2015computational"); ?> or directed&nbsp;<?php echo $references->cite("syed2019copaal"); ?> paths up to a given threshold length that support the input edge. The relatedness between input edges and paths is computed using a mutual information function, such as normalised pointwise mutual information&nbsp;<?php echo $references->cite("bouma2009normalized"); ?>. Supervised approaches rather extract features for input edges from external knowledge graphs&nbsp;<?php echo $references->cite("sun2011pathsim,zhao2015automatic,lao2010relational"); ?> and train a classification model to label the edges as true or false. An important set of features are <em>metapaths</em>, which encode sequences of predicates that correlate positively with the edge label of the input edge. Amongst such works, PredPath&nbsp;<?php echo $references->cite("shi2016discriminative"); ?> automatically extracts metapaths based on type information. Several approaches rather encode the reference nodes and edges using graph embeddings (see Section&nbsp;<?php echo ref("ssec:embeddings"); ?>), which are then used to estimate the plausibility of the input edge being validated.</p>

		<h4 id="sssec-inconsistency-repairs" class="subsection">Inconsistency repairs</h4>
		<p>Ontologies can contain axioms – such as disjointness – that lead to inconsistencies. While such axioms can be provided by experts, they can can also be derived through symbolic learning, as discussed in Section&nbsp;<?php echo ref("ssec:symlearn"); ?>. Such axioms can then be used to detect inconsistencies. With respect to correcting a knowledge graph, however, detecting inconsistencies is not enough: techniques are also required to <em>repair</em> such inconsistencies, which itself is not a trivial task. In the simplest case, we may have an instance of two disjoint classes, such as that <span class="gnode">Santiago</span> is of type <span class="gnode">City</span> and <span class="gnode">Airport</span>, which are stated or found to be disjoint. To repair the inconsistency, it would be preferable to remove only the “incorrect” class, but which should we remove? This is not a trivial question, particularly if we consider that one edge can be involved in many inconsistencies, and one inconsistency can involve many edges. The issue of computing repairs becomes more complex when entailment is considered, where we not only need to remove the stated type, but also all of the ways in which it might be entailed; for example, removing the edge <?php echo gedge("Santiago","type","Airport"); ?> is insufficient if we further have an edge <?php echo gedge("Arica","flight","Santiago"); ?> combined with an axiom <?php echo gedge("flight","range","Airport"); ?>. <?php echo $references->citet("TopperKS12"); ?> suggest potential repairs for such violations – remove a domain/range constraint, remove a disjointness constraint, remove a type edge, or remove an edge with a domain/range constraint – where one is chosen manually. In contrast, <?php echo $references->citet("BonattiHPS11"); ?> propose an automated method to repair inconsistencies based on <em>minimal hitting sets</em>&nbsp;<?php echo $references->cite("Reiter87"); ?>, where each set is a minimal explanation for an inconsistency. The edges to remove are chosen based on scores of the trustworthiness of their sources and how many minimal hitting sets they are either elements of or help to entail an element of, where the knowledge graph is revised to avoid re-entailment of the removed edges. Rather than repairing the data, another option is to evaluate queries under inconsistency-aware semantics, such as returning <em>consistent answers</em> valid under every possible repair&nbsp;<?php echo $references->cite("LukasiewiczMS13"); ?>.</p>
		</section>

		<section id="ssec-other-refinement-tasks" class="section">
		<h3>Other Refinement Tasks</h3>
		<p>In comparison to the quality clusters discussed in Chapter&nbsp;<?php echo ref("chap:quality"); ?>, the refinement methods discussed herein address particular aspects of the accuracy, coverage, and coherency dimensions. Beyond these, one could conceive of further refinement methods to address further quality issues of knowledge graphs, such as succinctness. In general, however, the refinement tasks of <em>knowledge graph completion</em> and <em>knowledge graph correction</em> have received the majority of attention until now. For further details on knowledge graph refinement, we refer to the survey by <?php echo $references->citet("Paulheim17"); ?>.</p>
		</section>
	</section>
